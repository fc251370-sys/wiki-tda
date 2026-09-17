function hasPermission(required){
    const permissions = JSON.parse(localStorage.getItem('permissions') || [])
   
    
    if(Array.isArray(required)){
        return required.some(permission=>permissions.includes(permission))
    }
    return permissions.includes(required)
}
export default {
    mounted(el, bind){
        if(!hasPermission(bind.value)){
            el.remove();
        }
    }
}