# Follow-Up Calls — User Guide

## Overview

Follow-up calls let your agent **automatically call customers back** around key moments — for example, a reminder the day before an appointment, a satisfaction survey after a booking, or a callback after an inbound call. You define the rules once on the agent; the system creates and places the calls at the right time on its own.

Each follow-up call is an **outbound call** placed by the same agent, and it carries a short instruction telling the agent why it's calling (a reminder, a survey, etc.).

---

## Prerequisites

- A Phone Call Agent with a **phone number attached** (Step 3 of the editor) — required to place any call.
- For **booking-based** rules: the **Booking Assistant** must be enabled and configured on the agent (Step 1).
- Your plan must allow follow-up calls (see [Plan Requirements](#plan-requirements)).

---

## Plan Requirements

Follow-up calling is controlled by your subscription plan:

| Option | Meaning |
|--------|---------|
| **Enable Follow-Up Calls** | Master switch for automatic follow-up/callback calls. |
| **Enable Outbound Calls** | Follow-ups are outbound calls, so outbound must also be enabled. If outbound is off, follow-ups are off too. |

If your plan doesn't allow follow-up calls, the Follow-Up section is hidden in the agent editor and no follow-up calls are scheduled.

---

## How it works

1. You add one or more **rules** to the agent.
2. When a matching event happens (a booking is made, a call ends, etc.), the system calculates when to call and creates a **scheduled outbound call**.
3. When that time arrives, the call is placed automatically — subject to your outbound plan limits.
4. During the call, the agent follows your rule's instruction so it knows the purpose.

Follow-up calls appear in the Outbound call list with the source **Follow-up**.

---

## Configuring rules

1. Open the agent editor and go to **Step 3 — Phone Numbers**.
2. Open **Configure Follow-Up Calls**.
3. Add a rule and set:
   - **Enabled** — turn the rule on/off.
   - **When** — the trigger (see below).
   - **Offset** + **Unit** — how long before/after the event (e.g. `1 day`, `2 hours`, `30 minutes`).
   - **What the assistant should say** — the instruction for that call.
4. Add as many rules as you need, then **Save Follow-Up Rules**.

### Triggers

| Trigger | When the call is placed | Typical use |
|---------|-------------------------|-------------|
| **Before the booking** | *Offset* before the appointment start | Reminder call the day before |
| **After the booking** | *Offset* after the appointment end | Satisfaction survey |
| **After an inbound call** | *Offset* after an inbound call ends | Follow-up / survey with the caller |
| **When a booking is cancelled** | Right away when a booking is cancelled | Apology / rebooking offer |

Booking triggers (before / after / on cancel) only fire when the Booking Assistant is enabled and a booking is actually made through the agent. "After an inbound call" works for any inbound call and does not need booking.

### Personalizing the message

Use these placeholders in the instruction — they are filled in from the booking:

- `{name}` — the customer's name
- `{time}` — the appointment time
- `{date}` — the appointment date

**Example instruction:**
> Remind {name} about their appointment on {date} at {time} and confirm they will attend.

---

## What happens when bookings change

- **Booking cancelled** — any pending follow-up calls for that booking are cancelled. If you have an "on cancel" rule, that call is placed.
- **Booking rescheduled** — pending follow-up calls are re-timed to the new appointment.

---

## Important conditions

A follow-up call is only scheduled when **all** of these are true:

- The rule is **enabled**.
- The agent has a **phone number**.
- There is a **customer number to call** (from the booking, or the caller's number for inbound follow-ups).
- The calculated time is **in the future** (a reminder time already in the past is skipped).
- Your **plan allows** follow-up and outbound calls.

---

## Managing follow-up calls

Follow-up calls are ordinary outbound calls once created. On the **Outbound** page you can:

- Filter the list by the **Follow-up** source.
- See each one's scheduled time and status.
- Cancel or reschedule an individual pending follow-up call.

See the [Outbound Calls — User Guide](outbound-calls-usage.md) for status meanings and call logs.

---

## Troubleshooting

| Problem | Likely cause |
|---------|--------------|
| Follow-Up section is missing | Your plan doesn't allow follow-up (or outbound) calls. |
| No reminder was scheduled after a booking | Agent has no phone number, no customer number was captured, the rule is disabled, or the calculated time was already in the past. |
| Follow-up call never placed | Its scheduled time hasn't arrived, or your outbound daily/seconds allowance is exhausted (it retries on following days). |
| Reminders still go out after a cancellation | Cancellations only clean up follow-ups when the booking is cancelled **through the agent**; externally cancelled bookings aren't detected. |

---

## Related guides

- [Outbound Calls — User Guide](outbound-calls-usage.md)
- [Booking configuration — Calendly](booking-calendly-configuration.md)
- [Booking configuration — ElevenLabs](booking-elevenlabs-configuration.md)
