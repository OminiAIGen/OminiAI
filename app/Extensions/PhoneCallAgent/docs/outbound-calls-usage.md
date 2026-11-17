# Outbound Calls — User Guide

## Overview

Outbound calling lets your Phone Call Agent **place** calls, not just answer them. You can dial a single number, schedule a call for later, or run a bulk campaign that calls a whole list of numbers. The same agent (voice, language, instructions, knowledge base) is used for outbound calls.

Works with both providers — ElevenLabs and Twilio. The agent must have a phone number attached before it can place calls.

---

## Prerequisites

- A Phone Call Agent that is fully configured (voice, instructions, etc.).
- A **phone number attached** to that agent (Step 3, "Phone Numbers", in the agent editor).
- Your plan must allow outbound calling (see [Plan Requirements](#plan-requirements)).

---

## Plan Requirements

Outbound calling is controlled by your subscription plan. An administrator sets these on each plan:

| Option | Meaning |
|--------|---------|
| **Enable Outbound Calls** | Master switch. If off, no outbound calls or campaigns can be placed on this plan. |
| **Outbound Seconds Limit** | Total outbound talk-time per month. `-1` = unlimited, `0` = disabled, `>0` = max seconds. |
| **Outbound Calls Per Day** | How many outbound calls can be **placed** per day. `-1` = unlimited, `0` = disabled, `>0` = max per day. |

If you hit a limit, calls already scheduled stay in the queue and are placed automatically on following days as your allowance frees up. A single "call now" that exceeds your limit fails immediately with a reason.

---

## Opening the Outbound page

From the Phone Call Agent dashboard, open the **Outbound** page. From there you can:

- Place a single call (now or scheduled)
- Create and manage campaigns
- Review the call list, filter it, and open per-call logs

---

## Place a single call

1. Choose the **agent** that will make the call (only agents with a phone number appear).
2. Enter the **destination number** in international format, e.g. `+14155552671`.
3. Leave the schedule empty to **call now**, or pick a **future date/time** to schedule it.
4. Confirm.

- **Call now** — the call is placed within moments.
- **Scheduled** — the call sits as *Pending* until its time arrives, then is placed automatically.

A past date/time is treated as "call now".

---

## Run a campaign (bulk calls)

A campaign calls a list of numbers with one agent.

1. Give the campaign a **name**.
2. Choose the **agent**.
3. Paste or upload the **list of numbers** (international format, up to 1000).
4. Optionally set a **start time** to schedule the whole campaign.
5. Create.

The system creates one pending call per number. Calls are placed automatically, spread across days if needed to respect your **Outbound Calls Per Day** limit — so a large list is worked through over time rather than all at once.

### Editing a campaign

While a campaign is still running (not completed or cancelled) you can:

- **Rename** it.
- **Reschedule** it — moves all not-yet-placed calls to the new time.
- **Change the number list** — newly added numbers are queued; removed numbers are dropped **only if they haven't been dialed yet**. Numbers already called are left untouched.

### Cancelling

- **Cancel a campaign** — all not-yet-placed calls are cancelled. Calls already placed are kept in history.
- **Cancel a single scheduled call** — only works while the call is still *Pending* (not yet dialed).

---

## Understanding call status

| Status | Meaning |
|--------|---------|
| **Pending** | Scheduled/queued, not yet dialed. |
| **Queued** | Handed to the provider, dialing. |
| **Ringing / Answered** | In progress. |
| **Completed** | Finished normally. |
| **Failed** | Could not be placed (see the reason in the call log). |
| **Missed** | No answer. |
| **Cancelled** | You cancelled it before it was placed. |

Each call also has a **source** so you can tell where it came from:

- **Manual** — you placed it from the single-call form.
- **Campaign** — created by a campaign.
- **Follow-up** — created automatically by a follow-up rule (see [Follow-Up Calls](follow-up-calls-usage.md)).

You can filter the call list by status, agent, source, and destination number.

---

## Call logs

Open any call to see its technical log: status, provider call id, timestamps, duration, and — if it failed — the failure reason. This is the raw call record, separate from the conversation transcript.

---

## Troubleshooting

| Problem | Likely cause |
|---------|--------------|
| Agent not listed in the dropdown | It has no phone number attached. Attach one in Step 3 of the agent editor. |
| "Outbound calls are not available on your plan" | Outbound is disabled, or the seconds/daily limit is `0`, on your plan. |
| Scheduled call never places | Its time hasn't arrived yet, or your daily/seconds allowance is exhausted (it will retry on following days). |
| Single "call now" failed instantly | You are over a plan limit; a lone call can't wait, so it fails. Schedule it instead, or upgrade. |
| Call shows Failed with a provider reason | Check the call log — usually a bad number, provider credentials, or the agent's number isn't configured for outbound. |

---

## Related guides

- [Follow-Up Calls — User Guide](follow-up-calls-usage.md)
- [ElevenLabs configuration](elevenlabs-configuration.md)
- [Twilio configuration](twilio-configuration.md)
