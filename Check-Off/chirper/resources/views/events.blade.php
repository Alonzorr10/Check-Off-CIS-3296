<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chiron+GoRound+TC:wght@200..900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body {font-family: 'Chiron GoRound TC', sans-serif;}</style>
</head>
<body>
    <div class="page active" id="page-home">
    <div style="margin-bottom:1.5rem">
      <div class="page-title">Welcome back</div>
      <div class="page-sub">Manage your events and approve incoming payments.</div>
    </div>
    <div class="summary-row">
      <div class="summary-card"><div class="summary-label">Owed to you</div><div class="summary-val" id="sumOwed" style="color:#1D9E75">—</div></div>
      <div class="summary-card"><div class="summary-label">Awaiting approval</div><div class="summary-val" id="sumReview" style="color:#378ADD">—</div></div>
      <div class="summary-card"><div class="summary-label">Active events</div><div class="summary-val" id="sumEvents">—</div></div>
    </div>
    <div class="section-header">
      <div style="font-size:14px;font-weight:500">Your Events</div>
      <button class="btn btn-primary btn-sm" onclick="toggleNewEvent()">+ New Event</button>
    </div>
    <div class="new-event-form" id="newEventForm" style="display:none">
      <div class="form-row"><div class="form-label">Event name</div><input class="form-input" id="newEventName" placeholder="e.g. Weekend Trip"></div>
      <div style="display:flex;gap:8px;justify-content:flex-end">
        <button class="btn btn-sm" onclick="toggleNewEvent()">Cancel</button>
        <button class="btn btn-primary btn-sm" onclick="createEvent()">Create</button>
      </div>
    </div>
    <div id="eventList"></div>
  </div>

</body>
</html>