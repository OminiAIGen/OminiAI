# Auto Edit Videos — Setup & Usage

> Technical reference: API hosts, wiring, billing internals, routes, troubleshooting.
> For a plain-language walkthrough aimed at admins and end users, see
> [user-guide.md](./user-guide.md).

## Overview

Auto Edit Videos wraps the [Rendley API](https://docs.rendley.com). A user uploads raw
footage (or pastes a public media URL), describes the edit in plain language, and
Rendley's agent plans the cut — trims, captions, music, transitions — then `/v1/export`
renders the final MP4.

**Flow:**

```
User adds sources + prompt
  → POST mcp.rendley.com/v1/agent          (agent plans the edit)
  → poll job on the agent host             → session = Ready
  → POST api.rendley.com/v1/export/cost    (price it)
  → POST api.rendley.com/v1/export         (render it)
  → poll job on api.rendley.com            → session = Completed
  → file downloaded to local storage, credits debited once
```

Follow-up chat ("make it shorter", "louder music") reuses the stored `project_id` +
`thread_id`, so the agent does not have to be re-told the whole brief.

### Two API hosts — do not merge them

Rendley serves the auto-edit agent from a **different host** than the rest of the API.
This is the single most common misconfiguration.

| Call | Host |
|---|---|
| Auto edit agent (`/v1/agent`) | `https://mcp.rendley.com` |
| Export, export cost, jobs, projects, account | `https://api.rendley.com` |

`api.rendley.com` authenticates the `/v1/agent` **prefix** (it owns the SSE route
`/v1/agent/sessions`) but routes nothing at `/v1/agent` itself. A valid key therefore
clears auth and falls through to the Go router's plain-text `404 page not found` — which
looks like a missing project, not a wrong URL.

Diagnostic that settles it in three calls:

```bash
curl -si https://mcp.rendley.com/v1/agent   | head -1   # 405 — route exists, POST only
curl -si https://api.rendley.com/v1/agent   | head -1   # 404 — no such route
curl -si https://api.rendley.com/v1/users/me -H "Authorization: Bearer $KEY" | head -1   # 200
```

---

## Prerequisites

- A [Rendley](https://rendley.com) account with an API key **and credits on it**
- The site reachable from the public internet, or a public CDN/mirror prefix for uploads
- `php artisan schedule:run` wired into cron (see [Step 6](#step-6--cron-required))
- Extension registered in the marketplace (`auto-edit-videos`)

---

# Part 1 — Setup (admin)

## Step 1 — Get a Rendley API key

1. Log in to the [Rendley dashboard](https://docs.rendley.com/api/getting-started/)
2. Go to **API Keys**, create a key, copy it
3. Confirm the account has credits — a request with no credits comes back `402` and the
   user sees *"The video service is out of credits. Please contact the administrator."*

## Step 2 — Admin settings

**Dashboard → Admin → API Integration → Auto Edit Videos**
(`/dashboard/admin/auto-edit-videos/settings`)

The sidebar entry is registered under the `api_integration` parent in `MenuService`, not
under `settings` — the page's own title still reads *Rendley API Settings*.

| Field | Setting key | Required | Notes |
|---|---|:---:|---|
| Rendley API Key | `auto_edit_videos_api_key` | ✅ | Stored in the settings table, never exposed to end users. Rotatable without a deploy. |
| Public media URL | `auto_edit_videos_public_base_url` | ⚠️ | Prefix used to build the URL handed to Rendley. Leave empty when the site itself is publicly reachable. |
| Auto edit endpoint | `auto_edit_videos_agent_url` | ❌ | Leave empty. Defaults to `https://mcp.rendley.com/v1/agent`. Override only if Rendley moves the agent. |

Click **Test connection** after saving. It calls `GET api.rendley.com/v1/users/me` — the
cheapest authenticated call Rendley exposes — and reports the account it authenticated as.
Nothing is spent.

> Saving settings also rebuilds the cached sidebar menu, because the entry's visibility
> depends on them.

## Step 3 — Public media URL (the other common failure)

Rendley fetches **every source over the public internet**. It never receives the file
body; it receives a URL and downloads it itself.

Uploads are refused *before* they reach Rendley when the resulting URL is unreachable —
otherwise Rendley fails with `MEDIA_MISSING_PERMANENT_URL`, which reads as a vendor
outage. Blocked by default:

| Kind | Blocked values |
|---|---|
| Host suffixes | `.test`, `.local`, `.localhost`, `.internal`, `.invalid` |
| Hosts | `localhost`, `127.0.0.1`, `::1`, `0.0.0.0`, private ranges |
| Schemes | anything that is not `http`/`https` |

So on a Herd/local install (`magicai.test`) uploads will always be refused. Two ways
forward:

- **Production:** leave *Public media URL* empty; `APP_URL` is used.
- **Local dev / private network:** set *Public media URL* to a public mirror or CDN prefix
  that serves the same `public/uploads` tree (e.g. `https://cdn.example.com/uploads`), or
  use the **paste a public URL** input instead of uploading.

Files are stored on the `public` disk under `media/videos/u-{userId}/auto-edit/`.

## Step 4 — Plan access and limits

**Dashboard → Admin → Plans → (edit plan)**

1. **AI features step** — enable **Auto Edit Videos** (`auto_edit_videos`). Without it the
   user is redirected to the pricing page by the plan gate.
2. **Second step** — two limit fields, both monthly and both defaulting to `-1`:

| Field | Plan column | `-1` | `0` |
|---|---|---|---|
| Auto Edit Videos — seconds per month | `auto_edit_videos_seconds_limit` | unlimited | feature off |
| Auto Edit Videos — videos per month | `auto_edit_videos_renders_limit` | unlimited | feature off |

Limits are counted from **completed, billable renders in the current calendar month**.
Admins and shared-credit users bypass them.

## Step 5 — Credits

Output is billed per second against the `rendley-auto-edit` entity (engine **Rendley**).

| | Value |
|---|---|
| Entity | `EntityEnum::RENDLEY_AUTO_EDIT` |
| Driver | `RendleyAutoEditDriver` (per-second) |
| Default credit rate | `4.0` credits per second of output |
| Reference list price | `$0.025` per second of output |

Adjust the rate the same way as any other entity in the credit configuration.

**When the debit happens:** exactly once, when a render **completes** and the real output
duration is known. A failed or canceled job never charges — which is why there is no refund
path in this extension. The completion transition is claimed with a conditional update, so
two concurrent pollers cannot double-charge.

## Step 6 — Cron (required)

Rendley publishes **no webhooks**. Every job is polled. The default queue connection is
`sync`, where a self-re-dispatching poll job cannot work, so a scheduled sweep is the only
thing that rescues an edit whose queued job never ran.

```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

The provider registers `auto-edit-videos:poll` `everyMinute()->withoutOverlapping()`.
Run it by hand when debugging:

```bash
php artisan auto-edit-videos:poll            # sweep stale sessions + renders
php artisan auto-edit-videos:poll --limit=10 # cap one pass
```

It only touches rows that are stale (`stale_after`, default 60s) and under the attempt
ceiling (`max_attempts`, default 240). The ceiling is deliberate: without it a job that
never reaches a terminal state re-queues itself forever.

## Step 7 — Optional env overrides

Only needed to follow a vendor change; both have working defaults.

```env
AUTO_EDIT_VIDEOS_BASE_URL=https://api.rendley.com
AUTO_EDIT_VIDEOS_AGENT_URL=https://mcp.rendley.com/v1/agent
```

After changing either, run `php artisan optimize:clear` — the config is cached.

Everything else lives in `app/Extensions/AutoEditVideos/config/auto-edit-videos.php`:
poll cadence, export defaults (`1080p` / `h264` / `high`), the resolution, aspect-ratio and
target-length option lists, upload limits, and the blocked-host lists.

---

# Part 2 — Usage (end user)

**Dashboard → Auto Edit Videos** (`/dashboard/user/auto-edit-videos`)

## Start an edit

| Field | Required | Notes |
|---|:---:|---|
| What should the editor do? | ✅ | The brief, 1–5000 chars. Plain language: *"Cut this interview into a 90 second highlight reel, remove the silences, add captions and background music."* |
| Your footage | ✅ | Up to **10** files, **512 MB** each. Video, audio or images. Drag-and-drop, or paste a public media URL. |
| Target length | ❌ | `15 / 30 / 60 / 90 / 180` seconds, or *Let the editor decide*. |
| Resolution | ❌ | `720p` / `1080p` / `4K`. Default `1080p`. |
| Aspect ratio | ❌ | `16:9` / `9:16` / `1:1` / `4:5`. Default `16:9`. |
| Style notes | ❌ | Optional tone/brand notes, up to 2000 chars. |

Accepted source types: `video/mp4`, `video/quicktime`, `video/webm`, `video/x-m4v`,
`audio/mpeg`, `audio/wav`, `audio/x-wav`, `image/png`, `image/jpeg`, `image/webp`.

Rendley takes no duration or style parameters — its own guide says both belong in the
prompt — so *Target length* and *Style notes* are folded into the prompt text on the way out.
The thread still shows the user exactly what they typed.

## Session statuses

| Status | Meaning |
|---|---|
| **Planning the edit** | Agent job running. Polled automatically. |
| **Ready to export** | Plan is done. User can chat with it or export. |
| **Rendering** | Export job running. |
| **Completed** | MP4 ready, credits debited, file copied to local storage. |
| **Failed** | Terminal. Shows the reason. Not charged. |
| **Canceled** | Terminal. Not charged. |

The page polls itself, so a status changes without a manual refresh.

## Refine with follow-up messages

On a **Ready** session, type a follow-up ("make it 30 seconds", "bigger captions",
"drop the intro"). It reuses the same Rendley project and thread, so the agent edits the
existing plan rather than starting over. The session returns to **Planning** and comes back
**Ready**.

## Export

**Export** prices the exact job first (`/v1/export/cost`), re-checks plan limits and credit
balance, then queues the render. A user is never charged for a render they could not have
afforded.

When it completes, the file is copied from Rendley's time-limited storage URL onto local
storage, so the download link keeps working after Rendley's URL expires.

## Library

**Library** lists finished renders with thumbnail, duration and download. Multi-select
supports bulk delete. Completed renders also surface in the core **Video Studio** hub.

---

## Troubleshooting

| Symptom | Cause | Fix |
|---|---|---|
| *"The Rendley endpoint was not found. Check the agent endpoint URL…"* | Agent posted to the wrong host. | Clear the **Auto edit endpoint** setting (or set it to `https://mcp.rendley.com/v1/agent`), then `php artisan optimize:clear`. |
| *"Rendley could not find that project or job."* | Real missing resource — Rendley answered with its JSON error envelope. | Check `rendley_project_id` / `rendley_job_id` on the row. The project may have been deleted vendor-side. |
| *"The video service is out of credits."* | `402` from Rendley. | Top up the Rendley account. This message proves auth and routing are correct. |
| *"The Rendley API key is invalid."* | `401` / `403`. | Re-paste the key, then **Test connection**. |
| Upload refused immediately | The generated URL is not publicly reachable (`.test`, `localhost`, private IP). | Set **Public media URL**, or paste a public media URL instead. |
| Session stuck on **Planning** | Cron not running, so nothing polls. | Verify `schedule:run` in crontab; run `php artisan auto-edit-videos:poll` by hand. |
| Session stuck, `poll_attempts` at 240 | Hit the attempt ceiling. | Job never reached a terminal state vendor-side. Start a fresh session; a terminal-failed one never retries. |
| Menu entry missing | Cached menu, or plan feature off. | Re-save the settings page (it regenerates the menu); enable **Auto Edit Videos** on the plan. |
| Redirected to the pricing page | Plan gate. | Enable the feature on the plan and check both limit fields are not `0`. |

Every failed Rendley call is logged as `Rendley request failed` with method, URL, status and
raw body — that log line names the host, which is usually the answer.

---

## Reference

**Routes**

| Method | URI | Name |
|---|---|---|
| GET | `dashboard/user/auto-edit-videos` | `…auto-edit-videos.index` |
| POST | `dashboard/user/auto-edit-videos` | `…store` (throttle 5/min) |
| GET | `dashboard/user/auto-edit-videos/library` | `…library` |
| POST | `…/sources` | `…sources.store` (throttle 30/min) |
| DELETE | `…/sources/{source}` | `…sources.destroy` |
| GET | `…/sessions/{session}` | `…sessions.show` |
| GET | `…/sessions/{session}/check` | `…sessions.check` (throttle 60/min) |
| GET | `…/sessions/{session}/cost` | `…sessions.cost` |
| POST | `…/sessions/{session}/message` | `…sessions.message` (throttle 10/min) |
| POST | `…/sessions/{session}/export` | `…sessions.export` (throttle 5/min) |
| DELETE | `…/sessions/{session}` | `…sessions.destroy` |
| GET/POST | `dashboard/admin/auto-edit-videos/settings` | `…admin.auto-edit-videos.settings` / `.update` |
| POST | `dashboard/admin/auto-edit-videos/settings/test` | `…admin.auto-edit-videos.test` |

**Tables**

`ext_auto_edit_videos_sessions`, `ext_auto_edit_videos_messages`,
`ext_auto_edit_videos_sources`, `ext_auto_edit_videos_renders`, plus two columns added to
`plans`.

**Handy queries**

```sql
SELECT id, status, rendley_project_id, rendley_job_id, poll_attempts, error_message
FROM ext_auto_edit_videos_sessions ORDER BY id DESC LIMIT 5;

SELECT id, status, duration_seconds, credits, usage_deducted, error_message
FROM ext_auto_edit_videos_renders ORDER BY id DESC LIMIT 5;
```

**Tests**

```bash
php artisan test --compact tests/Feature/Extensions/AutoEditVideos
```

Covers the session lifecycle (including that the agent POST goes to `mcp.rendley.com` and
*not* to the api host), export and polling, billing, the poll command, settings, source
media and the studio page.
