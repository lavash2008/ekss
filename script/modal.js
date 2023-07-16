var modal=document.getElementById('modalWindow');
var button = document.getElementById('buttonModal');
var headModal = document.getElementById('headerModal');
var contentModal = document.getElementById('textModal');

button.onclick=function(){
    modal.style.display= 'none';
    modal.style.opacity= 0;
}

class Modal {
    constructor (content,head=""){

        headModal.innerHTML=head;
        contentModal.innerHTML=content;
    };
    show(){
        modal.style.display= 'flex';
        modal.style.opacity= 100;
    };
    hide(){
        modal.style.display= 'none';
    } 
}