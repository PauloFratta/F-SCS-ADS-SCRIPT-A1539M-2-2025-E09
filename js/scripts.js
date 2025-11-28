// js/scripts.js

function showSection(sectionId) {
    ['home','features','dashboard','kanban','login','register'].forEach(id => {
        const el = document.getElementById(id);
        if(el) el.style.display = 'none';
    });
    const target = document.getElementById(sectionId);
    if(target) target.style.display = 'block';
    window.scrollTo(0, 0);
}

// Modais
function openTaskModal() { document.getElementById('taskModal').style.display = 'flex'; }
function closeTaskModal() { document.getElementById('taskModal').style.display = 'none'; }

function openColumnModal() { document.getElementById('columnModal').style.display = 'flex'; }
function closeColumnModal() { document.getElementById('columnModal').style.display = 'none'; }

// Editar Coluna
function openEditColumnModal(id, title) {
    document.getElementById('edit-col-id').value = id;
    document.getElementById('edit-col-title').value = title;
    document.getElementById('delete-col-btn').href = 'columns/delete.php?id=' + id;
    document.getElementById('editColumnModal').style.display = 'flex';
}
function closeEditColumnModal() { document.getElementById('editColumnModal').style.display = 'none'; }

// Editar Tarefa
function openEditModal(task) {
    document.getElementById('edit-id').value = task.id;
    document.getElementById('edit-title').value = task.title;
    document.getElementById('edit-desc').value = task.description;
    document.getElementById('edit-deadline').value = task.deadline;
    document.getElementById('edit-priority').value = task.priority;

    // Tratamento para garantir que column_id ou current_column_id existam
    let colId = task.column_id || task.current_column_id;
    if(colId) document.getElementById('edit-status').value = colId;

    document.getElementById('delete-btn').href = 'tasks/delete.php?id=' + task.id;
    document.getElementById('editTaskModal').style.display = 'flex';
}
function closeEditModal() { document.getElementById('editTaskModal').style.display = 'none'; }

// Fechar ao clicar fora
window.onclick = function(e) {
    if(e.target.className === 'modal') e.target.style.display = 'none';
}