// admin.js - interactivity, smooth scroll, AJAX CRUD for events/schedules, modal create account

const $ = (sel, ctx=document) => ctx.querySelector(sel);
const $$ = (sel, ctx=document) => Array.from(ctx.querySelectorAll(sel));

// Sidebar hamburger (mobile)
const sidebar = $('#sidebar');
$('#hamburger')?.addEventListener('click', () => sidebar.classList.toggle('open'));

// Subnav scroll-to
$$('.sub-nav li').forEach(li => {
  li.addEventListener('click', () => {
    const target = li.getAttribute('data-target');
    if (target) {
      document.querySelector(target)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
      $$('.sub-nav li').forEach(x => x.classList.remove('highlight'));
      li.classList.add('highlight');
    } else if (li.id === 'openCreateAccountTop') {
      openCreateAccount();
    }
  });
});

// Create Account Modal open/close
const createAccountModal = $('#createAccountModal');
function openCreateAccount(){
  createAccountModal.setAttribute('aria-hidden', 'false');
}
function closeCreateAccount(){
  createAccountModal.setAttribute('aria-hidden', 'true');
}
$('#openCreateAccount')?.addEventListener('click', (e) => { e.preventDefault(); openCreateAccount(); });
$('#openCreateAccountTop')?.addEventListener('click', openCreateAccount);
$$('[data-close="modal"]').forEach(btn => btn.addEventListener('click', closeCreateAccount));
createAccountModal?.addEventListener('click', (e) => {
  if (e.target.matches('.modal-backdrop')) closeCreateAccount();
});

// Helper: fetch JSON
async function jfetch(url, options={}){
  const res = await fetch(url, { headers: { 'Accept': 'application/json' }, ...options });
  if (!res.ok) throw new Error(await res.text());
  return res.json();
}

// Render Event & Schedule cards
function cardTemplate(item, type){
  const dateEnd = item.date_end ? ` – ${item.date_end}` : '';
  return `<div class="${type === 'event' ? 'event-card green' : 'schedule-card'}" data-id="${item.id}">
    <div class="card-actions">
      <button class="icon-btn" data-action="delete" title="Delete"><span class="material-icons">delete</span></button>
    </div>
    <div class="card-title">
      <span class="material-icons">${type === 'event' ? 'calendar_today' : 'event'}</span>
      ${item.title}
    </div>
    <div class="card-dates">${item.date_start}${dateEnd}</div>
  </div>`;
}

async function loadLists(){
  try {
    const [events, schedules] = await Promise.all([
      jfetch('events_api.php'),
      jfetch('schedules_api.php')
    ]);

    const eventsList = $('#events-list');
    eventsList.innerHTML = events.map(e => cardTemplate(e, 'event')).join('') || '<p>No events yet.</p>';

    const schedulesList = $('#schedules-list');
    schedulesList.innerHTML = schedules.map(s => cardTemplate(s, 'schedule')).join('') || '<p>No schedules yet.</p>';

  } catch (err){
    console.error(err);
  }
}

// Add Event
$('#eventForm')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const fd = new FormData(e.target);
  try {
    const res = await jfetch('events_api.php', { method: 'POST', body: fd });
    e.target.reset();
    await loadLists();
    alert('Event added!');
  } catch (err){
    alert('Failed to add event: ' + err.message);
  }
});

// Add Schedule
$('#scheduleForm')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const fd = new FormData(e.target);
  try {
    const res = await jfetch('schedules_api.php', { method: 'POST', body: fd });
    e.target.reset();
    await loadLists();
    alert('Schedule added!');
  } catch (err){
    alert('Failed to add schedule: ' + err.message);
  }
});

// Delete (event delegation)
$('#events-list')?.addEventListener('click', async (e) => {
  const btn = e.target.closest('[data-action="delete"]');
  if (!btn) return;
  const card = btn.closest('[data-id]');
  const id = card?.getAttribute('data-id');
  if (!id) return;
  if (!confirm('Delete this event?')) return;
  try {
    await jfetch(`events_api.php?id=${encodeURIComponent(id)}`, { method: 'DELETE' });
    card.remove();
  } catch (err){
    alert('Failed to delete: ' + err.message);
  }
});

$('#schedules-list')?.addEventListener('click', async (e) => {
  const btn = e.target.closest('[data-action="delete"]');
  if (!btn) return;
  const card = btn.closest('[data-id]');
  const id = card?.getAttribute('data-id');
  if (!id) return;
  if (!confirm('Delete this schedule?')) return;
  try {
    await jfetch(`schedules_api.php?id=${encodeURIComponent(id)}`, { method: 'DELETE' });
    card.remove();
  } catch (err){
    alert('Failed to delete: ' + err.message);
  }
});

// Create Account
$('#createAccountForm')?.addEventListener('submit', async (e) => {
  e.preventDefault();
  const fd = new FormData(e.target);
  try {
    const res = await jfetch('accounts_api.php', { method: 'POST', body: fd });
    alert('Account created!');
    closeCreateAccount();
    // Optionally update counts
    await refreshCounts();
  } catch (err){
    alert('Failed to create account: ' + err.message);
  }
});

async function refreshCounts(){
  // lightweight endpoint to return counts could be added; for now, force reload numbers by hitting a small endpoint
  try{
    const res = await jfetch('accounts_api.php?counts=1');
    if (res && typeof res.students !== 'undefined'){
      $('#totalStudents').textContent = res.students;
      $('#totalTeachers').textContent = res.teachers;
      $('#activeClasses').textContent = res.classes;
    }
  }catch(e){ /* ignore */ }
}

// Initial load
document.addEventListener('DOMContentLoaded', async () => {
  await loadLists();
  await refreshCounts();
});
