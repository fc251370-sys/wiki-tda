# Pasos para conectar las acciones de carreras

Esta guía describe cómo conectar los botones **Editar** y **Eliminar** de la tabla de carreras con el backend existente. No implica borrar físicamente una carrera: el controlador usa la acción de eliminar para cambiar su estado.

## Flujo actual

- La tabla y los botones están en [`src/view/carrera/index.vue`](./src/view/carrera/index.vue). El botón **Ver** ya llama a `verCarrera`; **Editar** y **Eliminar** todavía no tienen eventos `@click`.
- El composable [`src/view/carrera/composable/carreraComposable.js`](./src/view/carrera/composable/carreraComposable.js) expone la lista y `obtenerCarreras()`, que carga los datos desde el store.
- El store [`src/store/carrera/useCarrera.js`](./src/store/carrera/useCarrera.js) solo tiene la acción `getCarreras()`.
- El servicio [`src/services/carrera/carrera.service.js`](./src/services/carrera/carrera.service.js) solo tiene la petición GET para listar.
- En [`apps/plantillawiki/routes/api.php`](../plantillawiki/routes/api.php) ya están declaradas las rutas `PUT /carreras/{id}` y `DELETE /carreras/{id}`.
- En [`apps/plantillawiki/app/Http/Controllers/Api/Carrera/CarreraController.php`](../plantillawiki/app/Http/Controllers/Api/Carrera/CarreraController.php), `update` edita `name` y `code`; `destroy` alterna el valor de `estado`.
- Axios agrega `/api` mediante su `baseURL` en [`src/utils/axios.js`](./src/utils/axios.js). Por eso, desde el servicio se usan rutas como `carreras/{id}`, sin repetir `/api`.

## Pasos

1. **Definir qué datos se editarán.**  
   El controlador valida `name` como requerido y permite `code` nulo. Al abrir la edición, carga en un formulario los valores actuales de la carrera seleccionada y conserva su `id`. No envíes `estado` como si fuera parte de esta edición: el controlador lo gestiona mediante la acción de cambio de estado.

2. **Agregar las llamadas HTTP al servicio.**  
   Junto a `carreras()` en `src/services/carrera/carrera.service.js`, añadir una función de actualización que use `PUT` a `carreras/{id}` y envíe los datos del formulario, y una función para la acción de eliminar que use `DELETE` a `carreras/{id}`. El identificador debe ser el `item.id` de la fila seleccionada.

3. **Exponer las operaciones en el store.**  
   Importar las nuevas funciones del servicio en `src/store/carrera/useCarrera.js` y crear acciones del store que las invoquen. Después de una respuesta exitosa, volver a cargar la lista con `getCarreras()` para reflejar los datos o el estado actualizado. No ocultar los errores en el store: propágalos para que la vista pueda cerrar el formulario solo cuando la petición termine bien y mostrar el error si falla. El interceptor de Axios ya presenta avisos para errores HTTP comunes.

4. **Exponer las acciones desde el composable.**  
   En `src/view/carrera/composable/carreraComposable.js`, crear métodos del composable que deleguen al store para actualizar y cambiar el estado. Mantener el acceso a la lista y a `obtenerCarreras()` que ya existen.

5. **Conectar el botón Editar en la vista.**  
   En `src/view/carrera/index.vue`, enlazar el `@click` del botón con un método que seleccione la fila y abra un diálogo o formulario de edición. Inicializar el formulario con `name` y `code` de esa carrera. Al guardar, llamar al método del composable con el `id` y los valores del formulario; validar que `name` no esté vacío, gestionar errores y cerrar el diálogo únicamente tras el éxito.

6. **Conectar el botón Eliminar como cambio de estado.**  
   Enlazar su `@click` con un método que reciba la carrera, pida confirmación y llame a la acción del composable. Como el backend alterna `estado`, actualizará `true` a `false`; no elimina el registro de la base de datos. Al terminar correctamente, recargar la tabla y reflejar el estado devuelto por el servidor.

7. **Evitar una reactivación involuntaria.**  
   El botón actual de eliminar se muestra también para carreras inactivas, mientras que cada llamada al endpoint vuelve a alternar el estado. Ocultar o deshabilitar ese botón cuando `item.estado` sea falso, o cambiar en otro momento el contrato del backend a una operación explícita de desactivación. Mientras el controlador siga alternando, no debe llamarse al endpoint para una carrera inactiva.

8. **Probar el flujo completo.**
   - Editar una carrera activa, guardar un `name` válido y comprobar que el `PUT` envía el `id` correcto y que la fila se actualiza.
   - Intentar guardar sin `name` y comprobar que se informa el error de validación.
   - Confirmar el cambio de estado de una carrera activa y comprobar que pasa a inactiva sin desaparecer de la base de datos.
   - Comprobar que una carrera inactiva no ofrece la acción que volvería a activarla accidentalmente.
   - Probar errores de red o del servidor y verificar que el diálogo no se cierre como si la operación hubiera sido exitosa.

## Consideraciones del controlador actual

- `destroy` cambia el estado, no ejecuta un borrado físico.
- El método `destroy` actual no comprueba explícitamente si la carrera existe antes de acceder a ella; conviene probar el caso de un `id` inexistente antes de conectar la acción en producción.
- El método `update` responde con error `422` si falta `name`; la petición debe contemplarlo.
