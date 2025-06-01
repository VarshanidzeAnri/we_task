document.addEventListener('DOMContentLoaded', function() {
    var select = document.querySelector('.original-select');
    if (!select) return;
    
    var wrapper = document.querySelector('.custom-multiselect');
    var tags = document.querySelector('.multiselect-selected-options');
    var dropdown = document.querySelector('.multiselect-dropdown');
    var search = document.querySelector('.multiselect-search');
    
    select.style.display = 'none';
    
    makeDropdown();
    updateTags();
    
    wrapper.onclick = function() {
        dropdown.style.display = 'block';
        search.focus();
    }
    
    document.onclick = function(e) {
        if (!wrapper.contains(e.target)) {
            dropdown.style.display = 'none';
        }
    }
    
    search.onkeyup = function() {
        var text = this.value.toLowerCase();
        var items = dropdown.children;
        
        for (var i = 0; i < items.length; i++) {
            var option = items[i];
            var content = option.innerText.toLowerCase();
            
            if (content.includes(text) && !option.classList.contains('selected')) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        }
    }
    
    function makeDropdown() {
        dropdown.innerHTML = '';
        
        for (var i = 0; i < select.options.length; i++) {
            var opt = select.options[i];
            
            var div = document.createElement('div');
            div.className = 'multiselect-option';
            div.innerHTML = opt.text;
            div.value = opt.value;
            
            div.onclick = function() {
                for (var j = 0; j < select.options.length; j++) {
                    if (select.options[j].value == this.value) {
                        select.options[j].selected = true;
                        
                        this.style.display = 'none';
                        
                        search.value = '';
                        updateTags();
                        break;
                    }
                }
            }
            
            dropdown.appendChild(div);
        }
    }
    
    function updateTags() {
        tags.innerHTML = '';
        
        for (var i = 0; i < select.options.length; i++) {
            if (select.options[i].selected) {
                var tag = document.createElement('div');
                tag.className = 'selected-option-tag';
                tag.innerHTML = select.options[i].text + 
                    ' <span class="remove-option">×</span>';
                tag.value = select.options[i].value;
                
                tag.querySelector('.remove-option').onclick = function(e) {
                    e.stopPropagation();
                    var value = this.parentNode.value;
                    
                    for (var j = 0; j < select.options.length; j++) {
                        if (select.options[j].value == value) {
                            select.options[j].selected = false;
                            break;
                        }
                    }
                    
                    updateTags();
                    
                    var items = dropdown.children;
                    for (var k = 0; k < items.length; k++) {
                        if (items[k].value == value) {
                            items[k].style.display = '';
                            break;
                        }
                    }
                }
                
                tags.appendChild(tag);
            }
        }
    }
});
