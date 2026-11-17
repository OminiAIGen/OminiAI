# Auto Edit Videos — Getting Started

A plain-language guide for admins setting it up and for people using it.

> Looking for API hosts, routes, database columns or credit maths?
> See [setup-and-usage.md](./setup-and-usage.md) — the technical version of this same guide.

---

## What is it?

Upload your raw footage, type what you want in normal English, and the AI editor cuts it
for you.

> *"Turn this interview into a 90 second highlight reel. Cut the silences, add captions and
> some upbeat background music."*

A few minutes later you get a finished MP4 you can download. If it is not quite right, you
just tell it what to change — no re-uploading, no starting over.

It can cut video, add captions, pick music, trim dead air, and resize for different
platforms (widescreen for YouTube, vertical for TikTok/Reels, square for feeds).

---

# Part 1 — For admins: turning it on

You only do this once. Budget about 15 minutes.

## What you need first

- A **Rendley** account with credits on it (this is the service that does the actual editing)
- Your site reachable on the public internet
- Cron set up on the server (your host or developer will know)

## 1. Get your Rendley key

1. Log in at **rendley.com**
2. Open the **API Keys** page
3. Create a key and copy it — it is a long string of letters and numbers
4. Check your credit balance while you are there. No credits means no edits.

## 2. Paste the key into MagicAI

Go to **Admin → API Integration → Rendley**.

| What you'll see | What to do |
|---|---|
| **Rendley API Key** | Paste the key you just copied |
| **Public media URL** | Leave empty for now — see step 3 |
| **Auto edit endpoint** | **Leave empty.** It fills itself in correctly. |

Click **Save**, then click **Test connection**.

- ✅ Green message with your account name → you're connected
- ❌ Red message → the key is wrong or has a typo. Re-copy it and try again.

> **Don't touch "Auto edit endpoint".** It looks like a blank you should fill in, but it is
> not. Leaving it empty is correct. Filling it in with a guess is the single most common
> way to break this feature.

## 3. Make sure the editor can reach your files

This is the one that surprises people, so it is worth a minute.

The AI editor lives on someone else's servers. When a user uploads a video, we don't post
the file across — we send a **link** to it, and the editor downloads it from there. That
link has to work from anywhere on the internet.

**If your site is live and public** — nothing to do. Leave *Public media URL* empty.

**If you're testing on a local machine** (an address ending in `.test`, or something like
`localhost` or `192.168.x.x`) — the editor cannot reach it. Uploads will be politely
refused with a message saying so, rather than mysteriously failing later. Two options:

- Put a public web address in **Public media URL** that serves the same uploads folder
- Or skip uploading and use the **paste a link** box with a video that is already online

## 4. Decide who gets it, and how much

Go to **Admin → Plans**, edit a plan.

**Turn it on:** in the AI features list, tick **Auto Edit Videos**. Without this tick,
people on that plan get sent to the pricing page instead.

**Set the allowance:** two boxes, both monthly.

| Box | Put in |
|---|---|
| Auto Edit Videos — seconds per month | Total finished video, in seconds. `-1` = unlimited, `0` = off |
| Auto Edit Videos — videos per month | How many finished videos. `-1` = unlimited, `0` = off |

Example: `600` seconds and `10` videos = up to ten videos a month, ten minutes of finished
footage in total.

Only **finished** videos count. Failed attempts don't use up anyone's allowance.

## 5. Turn on the scheduler (don't skip this)

The editing service has no way to ring us when a video is done, so we check on it every
minute. That check runs on the server's scheduler.

If the scheduler isn't running, videos get stuck saying **"Planning the edit"** forever.
Nothing is broken — nobody is checking.

Ask your developer or hosting provider to confirm this line is in the server's cron:

```
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

Most MagicAI installs already have it, because other features need it too.

## Setup checklist

- [ ] Rendley key saved, **Test connection** shows green
- [ ] Rendley account has credits
- [ ] Public media URL sorted (or site is public and it is empty)
- [ ] Feature ticked on at least one plan
- [ ] Monthly limits set
- [ ] Scheduler running

---

# Part 2 — For users: making a video

Open **Auto Edit Videos** from the sidebar.

## Step 1 — Add your footage

Drag your files onto the upload box, or click to browse. You can also paste a link to a
video that is already online.

- Up to **10 files** per edit
- Up to **512 MB** each
- Video, audio and images all welcome — mix them freely

Add the interview, the b-roll, your logo, a music track. The editor works out what to do
with them.

## Step 2 — Say what you want

Write it like you would say it to a human editor. Specific beats clever.

**Good:**
> Cut this down to the best 60 seconds. Remove the ums and long pauses. Add big captions at
> the bottom. Upbeat music, quiet under the talking.

**Too vague:**
> make it good

Mention anything you care about: pace, captions, music, what to cut, what to keep, what to
open with.

## Step 3 — Set the options (all optional)

| Option | What it does |
|---|---|
| **Target length** | Roughly how long the finished video should be. Leave on *Let the editor decide* if you don't mind. |
| **Resolution** | 720p (small file), 1080p (normal), 4K (big). |
| **Aspect ratio** | 16:9 widescreen for YouTube, 9:16 vertical for TikTok/Reels/Shorts, 1:1 square, 4:5 for feeds. |
| **Style notes** | Tone and branding. *"Punchy, upbeat, brand colours, bold captions."* |

Then hit **Start editing**.

## Step 4 — Wait a moment

You'll see the status change on its own — no need to refresh.

| Status | What's happening |
|---|---|
| **Planning the edit** | The AI is watching your footage and working out the cut. |
| **Ready to export** | It has a plan. Your turn — review, tweak, or render it. |
| **Rendering** | Making the actual video file. |
| **Completed** | Done. Download it. |
| **Failed** | Something went wrong. The reason is shown. You weren't charged. |

## Step 5 — Ask for changes

At **Ready to export**, type a follow-up like you're talking to an editor:

- *"Make it 30 seconds instead"*
- *"Bigger captions"*
- *"Drop the intro, start on the question"*
- *"Different music, something calmer"*

It remembers the whole conversation, so you never have to re-explain the brief or
re-upload anything. Go back and forth as often as you like — you are only charged when
you render a finished video.

## Step 6 — Export and download

Press **Export**. You'll see the cost before it starts. When it finishes, download it from
the session page or from **Library**, where all your finished videos live.

Your video is saved on this site, so the download link keeps working later.

---

## Questions people ask

**How much does it cost me?**
You're charged once, when a finished video is delivered, based on how long that video is.
Planning, chatting and changes are all free.

**What if it fails?**
You're not charged. Nothing comes off your monthly allowance either. Try again, or reword
the brief.

**Can I fix it without starting over?**
Yes — that's what the follow-up messages are for. Same conversation, same footage.

**How long does it take?**
Usually a few minutes. Longer footage and 4K take longer.

**Can I upload a whole hour of footage?**
Up to 10 files, 512 MB each. Longer footage means a longer wait.

**Where did my finished videos go?**
**Library**, in the sidebar. They also show up in Video Studio.

---

## When something goes wrong

| What you see | What it means | What to do |
|---|---|---|
| *"The video service is out of credits"* | The site's editing account has run dry. | Admin: top up the Rendley account. |
| *"The Rendley API key is invalid"* | Wrong or expired key. | Admin: re-paste the key, hit **Test connection**. |
| *"The Rendley endpoint was not found…"* | Someone typed something into the **Auto edit endpoint** box. | Admin: clear that box completely, save, ask your developer to clear the cache. |
| Upload rejected straight away | The editor can't reach your site to download the file. | Admin: see setup step 3. Meanwhile, paste a public link instead. |
| Stuck on **"Planning the edit"** for ages | Nobody is checking on the job — scheduler isn't running. | Admin: see setup step 5. |
| *"…not available in your current plan"* | Feature is off, or the monthly box is set to `0`. | Admin: tick the feature and set the limits. |
| *"You have reached your monthly limit"* | Allowance used up for this month. | Wait for the reset, or move to a bigger plan. |
| Menu item missing | Cached menu, or feature off for that plan. | Admin: re-save the Rendley API Settings page, check the plan. |

Still stuck? Note the exact wording of the message and pass it to your developer — the
server log records what the editing service actually said, and that usually settles it in
one look.
