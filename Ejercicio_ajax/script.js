document.addEventListener('DOMContentLoaded', function() {
    // Cargar los tipos de documento al cargar la página
    cargarTiposDocumento();
    
    // Manejar el envío del formulario
    document.getElementById('consultaForm').addEventListener('submit', function(e) {
        e.preventDefault();
        guardarDocumento();
    });
});

function cargarTiposDocumento() {
    fetch('get_tipos_documento.php')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('tipo_documento');
            data.forEach(tipo => {
                const option = document.createElement('option');
                option.value = tipo.id;
                option.textContent = tipo.nombre;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error al cargar tipos de documento:', error);
            document.getElementById('resultado').innerHTML = 
                '<p class="error">Error al cargar los tipos de documento</p>';
        });
}

function guardarDocumento() {
    const tipoDocumento = document.getElementById('tipo_documento').value;
    const numeroDocumento = document.getElementById('numero_documento').value;
    const resultadoDiv = document.getElementById('resultado');
    
    resultadoDiv.innerHTML = '<p>Procesando consulta...</p>';
    
    const formData = new FormData();
    formData.append('tipo_documento', tipoDocumento);
    formData.append('numero_documento', numeroDocumento);
    
    fetch('guardar_consulta.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultadoDiv.innerHTML = `
                <p class="success">Consulta registrada correctamente</p>
                <p><strong>Tipo Documento:</strong> ${data.tipo_documento}</p>
                <p><strong>Número Documento:</strong> ${data.numero_documento}</p>
                <p><strong>Fecha Consulta:</strong> ${data.fecha_consulta}</p>
            `;
        } else {
            resultadoDiv.innerHTML = `<p class="error">${data.message}</p>`;
        }
    })
    .catch(error => {
        console.error('Error en la consulta:', error);
        resultadoDiv.innerHTML = '<p class="error">Error al procesar la consulta</p>';
    });
}