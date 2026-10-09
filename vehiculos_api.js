document.addEventListener('DOMContentLoaded', () => {
    const marcaSelect = document.getElementById('marca');
    const modeloSelect = document.getElementById('modelo');
    const apiStatus = document.getElementById('api-status');
    const ayudaModelo = document.getElementById('modelo-ayuda');

    if (!marcaSelect || !modeloSelect) {
        return;
    }

    const seleccion = window.AUTOMARKET_API_FILTER || { marca: '', modelo: '' };
    const API_BASE = 'https://vpic.nhtsa.dot.gov/api/vehicles';

    function setStatus(texto, tipo = 'secondary') {
        apiStatus.textContent = texto;
        apiStatus.className = `badge text-bg-${tipo} align-self-start`;
    }

    function resetModelos(mensaje = 'Seleccioná una marca primero') {
        modeloSelect.innerHTML = `<option value="">${mensaje}</option>`;
        modeloSelect.disabled = true;
        ayudaModelo.textContent = '';
    }

    async function cargarMarcas() {
        try {
            setStatus('Cargando marcas...', 'secondary');

            const respuesta = await fetch(`${API_BASE}/GetMakesForVehicleType/car?format=json`);
            if (!respuesta.ok) {
                throw new Error('No se pudo consultar la API.');
            }

            const datos = await respuesta.json();
            const marcas = Array.isArray(datos.Results) ? datos.Results : [];

            if (marcas.length === 0) {
                throw new Error('La API no devolvió marcas.');
            }

            // Elimina duplicados por MakeId y ordena alfabéticamente.
            const unicas = [...new Map(marcas.map(marca => [marca.MakeId, marca])).values()]
                .sort((a, b) => String(a.MakeName).localeCompare(String(b.MakeName)));

            marcaSelect.innerHTML = '<option value="">Seleccionar marca</option>';

            unicas.forEach(marca => {
                const option = document.createElement('option');
                option.value = marca.MakeName;
                option.dataset.makeId = marca.MakeId;
                option.textContent = marca.MakeName;
                marcaSelect.appendChild(option);
            });

            marcaSelect.disabled = false;
            setStatus(`${unicas.length} marcas disponibles`, 'success');

            if (seleccion.marca) {
                const opcion = [...marcaSelect.options].find(
                    option => option.value.toLowerCase() === seleccion.marca.toLowerCase()
                );

                if (opcion) {
                    marcaSelect.value = opcion.value;
                    await cargarModelos(opcion.dataset.makeId, seleccion.modelo);
                }
            }
        } catch (error) {
            console.error(error);
            marcaSelect.innerHTML = '<option value="">No se pudieron cargar las marcas</option>';
            marcaSelect.disabled = true;
            resetModelos('No disponible');
            setStatus('API no disponible', 'danger');
            ayudaModelo.textContent = 'Revisá la conexión a Internet y actualizá la página.';
        }
    }

    async function cargarModelos(makeId, modeloSeleccionado = '') {
        if (!makeId) {
            resetModelos();
            return;
        }

        modeloSelect.disabled = true;
        modeloSelect.innerHTML = '<option value="">Cargando modelos...</option>';
        ayudaModelo.textContent = '';

        try {
            const respuesta = await fetch(`${API_BASE}/GetModelsForMakeId/${encodeURIComponent(makeId)}?format=json`);
            if (!respuesta.ok) {
                throw new Error('No se pudieron consultar los modelos.');
            }

            const datos = await respuesta.json();
            const modelos = Array.isArray(datos.Results) ? datos.Results : [];

            const nombres = [...new Set(
                modelos
                    .map(modelo => String(modelo.Model_Name || '').trim())
                    .filter(Boolean)
            )].sort((a, b) => a.localeCompare(b));

            modeloSelect.innerHTML = '<option value="">Todos los modelos</option>';

            nombres.forEach(nombre => {
                const option = document.createElement('option');
                option.value = nombre;
                option.textContent = nombre;
                modeloSelect.appendChild(option);
            });

            modeloSelect.disabled = false;
            ayudaModelo.textContent = `${nombres.length} modelo(s) encontrados.`;

            if (modeloSeleccionado) {
                const opcion = [...modeloSelect.options].find(
                    option => option.value.toLowerCase() === modeloSeleccionado.toLowerCase()
                );

                if (opcion) {
                    modeloSelect.value = opcion.value;
                }
            }
        } catch (error) {
            console.error(error);
            resetModelos('No se pudieron cargar los modelos');
            ayudaModelo.textContent = 'No se pudieron cargar los modelos desde la API.';
        }
    }

    marcaSelect.addEventListener('change', () => {
        const opcion = marcaSelect.options[marcaSelect.selectedIndex];
        const makeId = opcion?.dataset?.makeId || '';
        cargarModelos(makeId);
    });

    cargarMarcas();
});
