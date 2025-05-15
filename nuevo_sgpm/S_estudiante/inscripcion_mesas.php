<?php
include 'layout_estudiante.php';
?>

</div>
    <div class="contenido">
    <h1 style="text-align: center; margin-bottom: 20px; color: #f3545d; font-weight: 900;">
        Inscripción a Mesas de Exámenes del ISPM N°1
    </h1>
    <p class="instrucciones">
        Para inscribirse en la mesa de examen, seleccione una materia con cupos disponibles y presione el botón que dice "Inscribirse".<br> 
        <strong>Importante:</strong> la inscripción estará disponible hasta 36 horas antes del inicio de la mesa de examen. Asegúrese de completar el proceso con tiempo suficiente.
    </p>
    <div class="tabla-contenedor">
        <table id="tabla-materias">
            <thead>
                <tr>
                    <th>Unidad Curricular</th>
                    <th>Fecha</th>
                    <th>Tanda</th>
                    <th>Llamado</th>
              
                    <th>Inscribirse</th>
                </tr>
            </thead>
            <tbody id="materias-lista">
                <!-- Las materias se llenarán desde el backend -->
            </tbody>
        </table>
    </div>
</div>

<script>
const fechasInscritas = new Set();  // Mueve la declaración de fechasInscritas al ámbito global

document.addEventListener("DOMContentLoaded", function () {
    fetch('obtener_materias_mesas_finales.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            let materiasLista = document.getElementById("materias-lista");
            materiasLista.innerHTML = '';  

            if (data.length === 0) {
                materiasLista.innerHTML = '<tr><td colspan="6">No hay materias disponibles</td></tr>';
                return;
            }

            const calcularTurno = (fecha) => {
                const year = new Date(fecha).getFullYear();
                const parsedDate = new Date(fecha);

                if (parsedDate >= new Date(`${year}-07-01`) && parsedDate <= new Date(`${year}-08-31`)) return 1;
                if (parsedDate >= new Date(`${year}-11-01`) && parsedDate <= new Date(`${year}-12-31`)) return 2;
                if (parsedDate >= new Date(`${year}-02-01`) && parsedDate <= new Date(`${year}-03-31`)) return 3;
                return null;
            };

            const turnoActual = (() => {
                const now = new Date();
                const year = now.getFullYear();
                if (now >= new Date(`${year}-07-01`) && now < new Date(`${year}-11-01`)) return 1;
                if (now >= new Date(`${year}-11-01`) && now <= new Date(`${year}-12-31`)) return 2;
                if (now >= new Date(`${year}-02-01`) && now <= new Date(`${year}-03-31`)) return 3;
                return null;
            })();

            data.forEach(materia => {
                if (!materia.disponible) return;

                let turnoMateria = calcularTurno(materia.fecha);
                if (turnoMateria !== turnoActual) return;

                let row = document.createElement("tr");
                let cupoDisponible = parseInt(materia.cupo, 10);
                let llamadoDisplay = (materia.llamado == 1) ? 'Primer Llamado' : 'Segundo Llamado';
                let inscribirseEnlace = '';

                if (fechasInscritas.has(materia.fecha)) {
                    inscribirseEnlace = '<span style="color:red;">Solo puedes rendir una unidad curricular por día</span>';
                } else if (materia.inscrito) {
                    inscribirseEnlace = '<span style="color:blue;">Ya estás inscripto en esta mesa en este turno</span>';
                } else if (materia.nota_final >= 6 || materia.materia_aprobada == 1) {
                    inscribirseEnlace = '<span style="color:green;">Ya aprobaste esta unidad curricular</span>';
                } else if (materia.promocionada == 1) {  
                    inscribirseEnlace = '<span style="color:green;">Promocionaste esta materia, no necesitas rendirla.</span>';
                } else if (materia.bloqueo_inscripcion == 1) {  
                    inscribirseEnlace = '<span style="color:red;">No puedes inscribirte más en esta materia. Debes recursarla.</span>';
                } else if (materia.inscrito_mismo_llamado == 1) {
                    inscribirseEnlace = '<span style="color:red;">Ya estás inscrpito en esta unidad curricular para este llamado y turno</span>';
                } else if (cupoDisponible > 0) {
                    inscribirseEnlace = `<a href="#" onclick="inscribirse(${materia.idfechas_mesas_finales}, '${materia.fecha}', ${materia.tanda}, ${cupoDisponible}); return false;">Inscribirse</a>`;
                } else {
                    inscribirseEnlace = '<span style="color:red;">Cupo agotado</span>';
                }


                row.innerHTML = `
                    <td>${materia.materia}</td>
                    <td>${materia.fecha}</td>
                    <td>${materia.tanda}</td>
                    <td>${llamadoDisplay}</td>
                    <td>${inscribirseEnlace}</td>
                `;
                materiasLista.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            let materiasLista = document.getElementById("materias-lista");
            materiasLista.innerHTML = '<tr><td colspan="6">Error al cargar las materias</td></tr>';
        });
});

function inscribirse(idFecha, fecha, tanda, cupoActual) {
    if (cupoActual <= 0) {
        alert("Cupo agotado para esta mesa.");
        return;
    }

    // Verificación para evitar inscribir en la misma fecha
    if (fechasInscritas.has(fecha)) {
        alert("Solo puedes rendir una materia por día.");
        return;
    }

    fetch('inscribir_mesa.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ idFecha: idFecha, tanda: tanda })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Inscripción exitosa en la Tanda " + tanda);
            fechasInscritas.add(fecha);  // Marca la fecha como inscrita después de una inscripción exitosa
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

</script>







<style>
 
  .instrucciones {
        background-color: #fff8b0; /* Fondo amarillo claro */
        padding: 10px;
        border-radius: 5px;
        font-size: 16px;
        margin-bottom: 20px;
    }
  .tabla-contenedor {
    width: 100%;
    margin-top: 10px;
    overflow-x: auto;
  }

  #tabla-materias {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
    background-color: #ffffff;
    overflow: hidden;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  }

  #tabla-materias thead {
    background-color: #f3545d;
    color: #ffffff;
    font-weight: bold;
    position: sticky;
    top: 0;
    z-index: 1;
  }

  #tabla-materias th, #tabla-materias td {
    padding: 10px 8px; /* Reducción del padding */
    text-align: left;
    border-bottom: 1px solid #eaeaea;
    word-wrap: break-word; /* Permite el ajuste de línea */
    white-space: normal; /* Ajuste de línea activado */
  }

  #tabla-materias tbody tr {
    transition: background-color 0.3s, transform 0.1s;
  }

  #tabla-materias tbody tr:nth-child(even) {
    background-color: #f9f9f9;
  }

  #tabla-materias tbody tr:hover {
    background-color: #ffe4e8;
    transform: scale(1.01);
  }

  #tabla-materias td {
    overflow: visible;
  }

  #tabla-materias td a {
    color: #f3545d;
    text-decoration: none;
    font-weight: bold;
  }

  #tabla-materias td a:hover {
    color: #c13d4a;
    text-decoration: underline;
  }

  #tabla-materias td span {
    font-weight: bold;
    color: #c13d4a;
  }

  @media (max-width: 480px) {
    .contenido {
      padding: 10px;
    }

    #tabla-materias {
      font-size: 12px; /* Fuente más pequeña */
    }

    #tabla-materias th, #tabla-materias td {
      padding: 8px 5px; /* Más compacto */
    }
  }
</style>











</body>
</html>

