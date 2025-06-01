// Simple news JavaScript

// Wait for DOM to load
document.addEventListener('DOMContentLoaded', function() {
    // Get elements
    var sel = document.querySelector('.original-select');
    if (!sel) return;
    
    var cont = document.querySelector('.custom-multiselect-container');
    var tags = cont.querySelector('.multiselect-selected-options');
    var drop = cont.querySelector('.multiselect-dropdown');
    var inp = cont.querySelector('.multiselect-search');
    
    // Hide original select
    sel.style.display = 'none';
    
    // Add options to dropdown
    addOptions();
    showTags();
    
    // Toggle dropdown on click
    inp.onclick = function(e) {
        e.stopPropagation();
        drop.style.display = drop.style.display === 'block' ? 'none' : 'block';
    }
    
    // Hide dropdown when clicking outside
    document.onclick = function(e) {
        if (!cont.contains(e.target)) {
            drop.style.display = 'none';
        }
    }
    
    // Add options to dropdown
    function addOptions() {
        drop.innerHTML = '';
        for (var i = 0; i < sel.options.length; i++) {
            var opt = sel.options[i];
            var div = document.createElement('div');
            div.className = 'multiselect-option';
            div.setAttribute('data-value', opt.value);
            div.innerHTML = '<span>' + opt.text + '</span>';
            
            // Click handler
            div.onclick = function() {
                var val = this.getAttribute('data-value');
                for (var j = 0; j < sel.options.length; j++) {
                    if (sel.options[j].value == val) {
                        sel.options[j].selected = !sel.options[j].selected;
                        break;
                    }
                }
                showTags();
            };
            
            drop.appendChild(div);
        }
    }
    
    // Show selected tags
    function showTags() {
        tags.innerHTML = '';
        var count = 0;
        
        for (var i = 0; i < sel.options.length; i++) {
            if (sel.options[i].selected) {
                var tag = document.createElement('div');
                tag.className = 'selected-option-tag';
                tag.innerHTML = sel.options[i].text + ' <span class="remove-option" onclick="this.parentNode.remove();">×</span>';
                tags.appendChild(tag);
                count++;
            }
        }
        
        if (count == 0) {
            inp.placeholder = 'Select categories';
        } else {
            inp.placeholder = '';
        }
    }
});
