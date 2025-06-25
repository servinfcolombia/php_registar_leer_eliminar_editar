document.addEventListener('DOMContentLoaded', function() {
    const userForm = document.getElementById('userForm');
    const submitBtn = document.getElementById('submitBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const messageDiv = document.getElementById('message');
    const usersTable = document.getElementById('usersTable').getElementsByTagName('tbody')[0];
    
    let isEditMode = false;
    let currentUserId = null;
    
    // Cargar usuarios al iniciar
    loadUsers();
    
    // Manejar envío del formulario
    userForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(userForm);
        const action = isEditMode ? 'update' : 'create';
        formData.append('action', action);
        
        fetch('crud_users.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                resetForm();
                loadUsers();
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            showMessage('Error en la conexión', 'error');
            console.error('Error:', error);
        });
    });
    
    // Manejar botón cancelar
    cancelBtn.addEventListener('click', resetForm);
    
    // Función para cargar usuarios
    function loadUsers() {
        fetch('crud_users.php?action=read')
            .then(response => response.json())
            .then(data => {
                usersTable.innerHTML = '';
                
                if (data.length === 0) {
                    const row = usersTable.insertRow();
                    const cell = row.insertCell(0);
                    cell.colSpan = 5;
                    cell.textContent = 'No hay usuarios registrados';
                    return;
                }
                
                data.forEach(user => {
                    const row = usersTable.insertRow();
                    
                    row.insertCell(0).textContent = user.id;
                    row.insertCell(1).textContent = user.username;
                    row.insertCell(2).textContent = user.created_at;
                    row.insertCell(3).textContent = user.updated_at;
                    
                    const actionsCell = row.insertCell(4);
                    
                    const editBtn = document.createElement('button');
                    editBtn.textContent = 'Editar';
                    editBtn.className = 'update';
                    editBtn.addEventListener('click', () => editUser(user));
                    actionsCell.appendChild(editBtn);
                    
                    const deleteBtn = document.createElement('button');
                    deleteBtn.textContent = 'Eliminar';
                    deleteBtn.className = 'delete';
                    deleteBtn.addEventListener('click', () => deleteUser(user.id));
                    actionsCell.appendChild(deleteBtn);
                });
            })
            .catch(error => {
                showMessage('Error al cargar usuarios', 'error');
                console.error('Error:', error);
            });
    }
    
    // Función para editar usuario
    function editUser(user) {
        isEditMode = true;
        currentUserId = user.id;
        
        document.getElementById('userId').value = user.id;
        document.getElementById('username').value = user.username;
        document.getElementById('password').value = '';
        
        submitBtn.textContent = 'Actualizar';
        cancelBtn.classList.remove('hidden');
        
        document.getElementById('username').focus();
    }
    
    // Función para eliminar usuario
    function deleteUser(userId) {
        if (!confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
            return;
        }
        
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('userId', userId);
        
        fetch('crud_users.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showMessage(data.message, 'success');
                loadUsers();
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            showMessage('Error al eliminar usuario', 'error');
            console.error('Error:', error);
        });
    }
    
    // Función para resetear el formulario
    function resetForm() {
        userForm.reset();
        isEditMode = false;
        currentUserId = null;
        
        document.getElementById('userId').value = '';
        submitBtn.textContent = 'Guardar';
        cancelBtn.classList.add('hidden');
    }
    
    // Función para mostrar mensajes
    function showMessage(message, type) {
        messageDiv.textContent = message;
        messageDiv.className = `message ${type}`;
        messageDiv.classList.remove('hidden');
        
        setTimeout(() => {
            messageDiv.classList.add('hidden');
        }, 5000);
    }
});