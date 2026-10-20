export const getStoredList = (key) => {
  const storedValue = localStorage.getItem(key);

  if (!storedValue) {
    return [];
  }

  try {
    const value = JSON.parse(storedValue);
    return Array.isArray(value) ? value : [];
  } catch (error) {
    console.error(`No se pudo leer la lista "${key}" del almacenamiento local.`, error);
    return [];
  }
};
