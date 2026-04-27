<section class="bg-gray-50 p-4 min-h-[760px]">
    <div id="consultationNotesApp" class="rounded-md border border-gray-200 bg-white overflow-hidden">
        <div class="border-b border-gray-100 px-4 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900">Consultation Notes</h2>
                    <p class="mt-1 text-sm text-gray-500">Record and track all consultation sessions</p>
                </div>
                <button id="openConsultationNoteModal" type="button" class="h-10 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">
                    + Add Consultation Note
                </button>
            </div>
        </div>

        <div id="consultationNotesList" class="space-y-3 p-4"></div>

        <div id="consultationFormModal" class="fixed inset-0 z-[70] hidden" aria-hidden="true">
            <button type="button" data-slideover-overlay class="absolute inset-0 bg-slate-900/45 opacity-0 transition-opacity duration-300"></button>
            <div class="absolute inset-y-0 right-0 flex w-full justify-end overflow-hidden pointer-events-none">
                <div data-slideover-panel class="pointer-events-auto flex h-full w-full max-w-[720px] translate-x-full flex-col border-l border-gray-200 bg-white shadow-2xl transition-transform duration-300 ease-out">
                    <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5 sm:px-8">
                        <h3 id="consultationFormTitle" class="text-xl font-semibold text-gray-900">Add Consultation Note</h3>
                        <button id="closeConsultationFormModal" type="button" class="text-2xl leading-none text-gray-500 hover:text-gray-900">&times;</button>
                    </div>
                    <form id="consultationForm" class="flex min-h-0 flex-1 flex-col">
                        <div class="min-h-0 flex-1 overflow-y-auto p-6 sm:px-8">
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div class="md:col-span-2">
                                    <label for="noteTitle" class="mb-1 block text-sm font-medium text-gray-700">Note Title</label>
                                    <input id="noteTitle" type="text" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <p id="errorTitle" class="mt-1 hidden text-xs text-red-600">Note title is required.</p>
                                </div>
                                <div>
                                    <label for="consultationDate" class="mb-1 block text-sm font-medium text-gray-700">Consultation Date</label>
                                    <input id="consultationDate" type="date" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                    <p id="errorDate" class="mt-1 hidden text-xs text-red-600">Consultation date is required.</p>
                                </div>
                                <div>
                                    <label for="consultationAuthor" class="mb-1 block text-sm font-medium text-gray-700">Author / Created By</label>
                                    <input id="consultationAuthor" type="text" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                </div>
                                <div>
                                    <label for="consultationLinkedDeal" class="mb-1 block text-sm font-medium text-gray-700">Linked Deal</label>
                                    <input id="consultationLinkedDeal" type="text" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                </div>
                                <div>
                                    <label for="consultationLinkedActivity" class="mb-1 block text-sm font-medium text-gray-700">Linked Activity</label>
                                    <input id="consultationLinkedActivity" type="text" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="consultationCategory" class="mb-1 block text-sm font-medium text-gray-700">Tags or Category</label>
                                    <input id="consultationCategory" type="text" class="h-10 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="consultationSummary" class="mb-1 block text-sm font-medium text-gray-700">Consultation Summary</label>
                                    <textarea id="consultationSummary" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label for="consultationDetails" class="mb-1 block text-sm font-medium text-gray-700">Detailed Notes</label>
                                    <textarea id="consultationDetails" rows="5" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></textarea>
                                    <p id="errorBody" class="mt-1 hidden text-xs text-red-600">Provide a summary or detailed notes.</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 border-t border-gray-100 bg-white px-6 py-4 sm:px-8">
                            <button id="deleteConsultationNote" type="button" class="mr-auto hidden h-10 rounded-lg border border-red-200 bg-red-50 px-4 text-sm text-red-700 hover:bg-red-100">Delete</button>
                            <button id="cancelConsultationForm" type="button" class="h-10 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                            <button id="saveConsultationNote" type="submit" class="h-10 rounded-lg bg-blue-600 px-4 text-sm font-medium text-white hover:bg-blue-700">Save Consultation Note</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const app = document.getElementById('consultationNotesApp');
    if (!app) return;
    const csrfToken = <?php echo json_encode(csrf_token(), 15, 512) ?>;
    const baseUrl = <?php echo json_encode(route('company.consultation-notes.store', $company->id), 512) ?>;
    const updateTemplate = <?php echo json_encode(route('company.consultation-notes.update', [$company->id, '__NOTE__'])) ?>;
    const deleteTemplate = <?php echo json_encode(route('company.consultation-notes.destroy', [$company->id, '__NOTE__'])) ?>;
    const defaultAuthor = <?php echo json_encode($company->owner_name ?: 'John Admin', 15, 512) ?>;
    let notes = <?php echo json_encode($consultationNotes, 15, 512) ?>;
    let editNoteId = null;
    const notesList = document.getElementById('consultationNotesList');
    const formModal = document.getElementById('consultationFormModal');
    const form = document.getElementById('consultationForm');
    const deleteButton = document.getElementById('deleteConsultationNote');
    const fields = {
        title: document.getElementById('noteTitle'),
        consultationDate: document.getElementById('consultationDate'),
        author: document.getElementById('consultationAuthor'),
        linkedDeal: document.getElementById('consultationLinkedDeal'),
        linkedActivity: document.getElementById('consultationLinkedActivity'),
        summary: document.getElementById('consultationSummary'),
        details: document.getElementById('consultationDetails'),
        category: document.getElementById('consultationCategory'),
    };
    const errors = {
        title: document.getElementById('errorTitle'),
        consultationDate: document.getElementById('errorDate'),
        body: document.getElementById('errorBody'),
    };
    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[char]));
    const formatDate = (value) => value ? new Date(value + 'T00:00:00').toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' }) : '-';
    const sendJson = async (url, method, payload) => {
        const response = await fetch(url, { method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: payload ? JSON.stringify(payload) : null });
        if (!response.ok) throw new Error('Request failed');
        return response.json();
    };
    const showModal = () => { formModal.classList.remove('hidden'); requestAnimationFrame(() => { formModal.querySelector('[data-slideover-overlay]').classList.remove('opacity-0'); formModal.querySelector('[data-slideover-panel]').classList.remove('translate-x-full'); }); };
    const hideModal = () => { formModal.querySelector('[data-slideover-overlay]').classList.add('opacity-0'); formModal.querySelector('[data-slideover-panel]').classList.add('translate-x-full'); setTimeout(() => formModal.classList.add('hidden'), 300); };
    const resetValidation = () => Object.values(errors).forEach((el) => el.classList.add('hidden'));
    const resetForm = () => { editNoteId = null; Object.values(fields).forEach((field) => field.value = ''); fields.author.value = defaultAuthor; deleteButton.classList.add('hidden'); resetValidation(); };
    const renderNotes = () => {
        if (!notes.length) { notesList.innerHTML = '<div class="rounded-xl border border-gray-200 bg-white px-4 py-8 text-center text-sm text-gray-500">No consultation notes yet.</div>'; return; }
        notes.sort((a, b) => new Date(b.consultationDate || 0) - new Date(a.consultationDate || 0));
        notesList.innerHTML = notes.map((note) => `<article class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm"><div class="flex items-start justify-between gap-3"><div class="min-w-0"><h3 class="text-xl font-semibold text-gray-900">${escapeHtml(note.title)}</h3><p class="mt-1 text-sm text-gray-600">${escapeHtml(note.summary || note.details || '')}</p><div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-gray-500"><span><i class="far fa-calendar mr-1"></i>${escapeHtml(formatDate(note.consultationDate))}</span><span><i class="far fa-user mr-1"></i>${escapeHtml(note.author || defaultAuthor)}</span>${note.linkedDeal ? `<span><i class="fas fa-handshake-angle mr-1"></i>${escapeHtml(note.linkedDeal)}</span>` : ''}${note.linkedActivity ? `<span><i class="fas fa-square-check mr-1"></i>${escapeHtml(note.linkedActivity)}</span>` : ''}</div></div><div class="flex items-center gap-3 text-gray-500"><button type="button" class="note-edit hover:text-blue-600" data-note-id="${note.id}"><i class="far fa-pen-to-square"></i></button></div></div></article>`).join('');
    };
    const openEditModal = (noteId) => {
        const note = notes.find((item) => Number(item.id) === Number(noteId));
        if (!note) return;
        editNoteId = Number(note.id);
        fields.title.value = note.title || '';
        fields.consultationDate.value = note.consultationDate || '';
        fields.author.value = note.author || defaultAuthor;
        fields.linkedDeal.value = note.linkedDeal || '';
        fields.linkedActivity.value = note.linkedActivity || '';
        fields.summary.value = note.summary || '';
        fields.details.value = note.details || '';
        fields.category.value = note.category || '';
        deleteButton.classList.remove('hidden');
        resetValidation();
        showModal();
    };
    document.getElementById('openConsultationNoteModal').addEventListener('click', function () { resetForm(); showModal(); });
    document.getElementById('closeConsultationFormModal').addEventListener('click', hideModal);
    document.getElementById('cancelConsultationForm').addEventListener('click', hideModal);
    formModal.querySelector('[data-slideover-overlay]').addEventListener('click', hideModal);
    notesList.addEventListener('click', function (event) { const editButton = event.target.closest('.note-edit'); if (editButton) openEditModal(editButton.dataset.noteId); });
    deleteButton.addEventListener('click', async function () { if (editNoteId === null || !confirm('Delete this consultation note?')) return; await sendJson(deleteTemplate.replace('__NOTE__', editNoteId), 'DELETE'); notes = notes.filter((item) => Number(item.id) !== editNoteId); renderNotes(); hideModal(); });
    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        resetValidation();
        if (!fields.title.value.trim()) { errors.title.classList.remove('hidden'); return; }
        if (!fields.consultationDate.value) { errors.consultationDate.classList.remove('hidden'); return; }
        if (!fields.summary.value.trim() && !fields.details.value.trim()) { errors.body.classList.remove('hidden'); return; }
        const payload = { title: fields.title.value.trim(), consultation_date: fields.consultationDate.value, author: fields.author.value.trim() || defaultAuthor, linked_deal: fields.linkedDeal.value.trim(), linked_activity: fields.linkedActivity.value.trim(), summary: fields.summary.value.trim(), details: fields.details.value.trim(), category: fields.category.value.trim(), attachments: [] };
        const note = editNoteId === null ? await sendJson(baseUrl, 'POST', payload) : await sendJson(updateTemplate.replace('__NOTE__', editNoteId), 'PUT', payload);
        notes = editNoteId === null ? [...notes, note] : notes.map((item) => Number(item.id) === editNoteId ? note : item);
        renderNotes();
        hideModal();
    });
    renderNotes();
});
</script>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views/company/partials/consultation-notes-app.blade.php ENDPATH**/ ?>