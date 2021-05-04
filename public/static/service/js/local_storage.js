// 存储
function set_Storage(key,value){
    if (typeof(Storage) !== "undefined") {
        // Store
        web_cache = localStorage.setItem(key, value);
    } else {
        document.getElementById("#center").innerHTML = "抱歉！您的浏览器不支持 Web Storage ...";
    }
}
// 取出
function get_Storage(key){
    if (typeof(Storage) !== "undefined") {
        // Retrieve
        web_cache = localStorage.getItem(key);
        return web_cache;
    } else {
        document.getElementById("#center").innerHTML = "抱歉！您的浏览器不支持 Web Storage ...";
    }
}

// 真正地删除：将数据从本地删除
function remove_Storage(id){
    // 清空数据
    if(id == -1) {
        // 全部删除
        localStorage.clear();
    } else {
        // 删除一条记录
        localStorage.removeItem(id);
    }
}