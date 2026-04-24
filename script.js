// ===== FORM VALIDATION: Login Page =====
const loginForm = document.getElementById('loginForm');
if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
        const role = document.getElementById('role').value;
        const userId = document.getElementById('user_id').value.trim();
        const password = document.getElementById('password').value;

        if (!role) {
            e.preventDefault();
            alert('Please select a role.');
            return;
        }
        if (!userId) {
            e.preventDefault();
            alert('Please enter your User ID.');
            return;
        }
        if (!password) {
            e.preventDefault();
            alert('Please enter your password.');
            return;
        }
    });
}

// ===== CONFIRM DELETE =====
// Attach to any delete button/link with class "confirm-delete"
document.querySelectorAll('.confirm-delete').forEach(function(el) {
    el.addEventListener('click', function(e) {
        const message = this.getAttribute('data-msg') || 'Are you sure you want to delete this record?';
        if (!confirm(message)) {
            e.preventDefault();
        }
    });
});

// ===== RESULT HIGHLIGHTING =====
// Automatically colour rows in results tables based on marks
function highlightResults() {
    document.querySelectorAll('.result-row').forEach(function(row) {
        const markCell = row.querySelector('.mark-value');
        if (!markCell) return;

        const mark = parseFloat(markCell.textContent);
        if (isNaN(mark)) return;

        if (mark >= 80) {
            row.classList.add('row-distinction');
            markCell.classList.add('mark-distinction');
        } else if (mark >= 50) {
            row.classList.add('row-pass');
            markCell.classList.add('mark-pass');
        } else {
            row.classList.add('row-fail');
            markCell.classList.add('mark-fail');
        }
    });
}

// ===== LIVE MARKS CALCULATION =====
// For assessor marks entry – calculates weighted total as user types
function setupLiveCalculation() {
    const inputs = document.querySelectorAll('.mark-input');
    if (!inputs.length) return;

    inputs.forEach(function(input) {
        input.addEventListener('input', calculateTotal);
    });
    calculateTotal(); // run once on load
}

function calculateTotal() {
    let total = 0;
    document.querySelectorAll('.mark-input').forEach(function(input) {
        const mark   = parseFloat(input.value) || 0;
        const weight = parseFloat(input.getAttribute('data-weight')) || 0;
        const weighted = (mark * weight) / 100;

        // Update the weighted display cell next to this input
        const row = input.closest('tr');
        if (row) {
            const totalCell = row.querySelector('.total-cell');
            if (totalCell) totalCell.textContent = weighted.toFixed(2);

            // Validate: mark must be 0–100
            if (mark < 0 || mark > 100) {
                input.style.borderColor = '#e74c3c';
            } else {
                input.style.borderColor = '#dce3ec';
            }
        }
        total += weighted;
    });

    // Update the grand total display
    const grandTotal = document.getElementById('grand-total');
    if (grandTotal) {
        grandTotal.textContent = total.toFixed(2);

        // Change colour based on score
        grandTotal.className = '';
        if (total >= 80) grandTotal.style.color = '#856404';
        else if (total >= 50) grandTotal.style.color = '#155724';
        else grandTotal.style.color = '#721c24';
    }
}

// ===== SEARCH / FILTER TABLE =====
function setupSearch(inputId, tableId) {
    const searchInput = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    if (!searchInput || !table) return;

    searchInput.addEventListener('keyup', function() {
        const query = this.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(function(row) {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });
}

// ===== MARKS ENTRY: validate before submit =====
function validateMarksForm() {
    const inputs = document.querySelectorAll('.mark-input');
    for (let i = 0; i < inputs.length; i++) {
        const val = parseFloat(inputs[i].value);
        if (isNaN(val) || val < 0 || val > 100) {
            alert('All marks must be between 0 and 100.');
            inputs[i].focus();
            return false;
        }
    }
    return true;
}

// ===== RUN ON PAGE LOAD =====
document.addEventListener('DOMContentLoaded', function() {
    highlightResults();
    setupLiveCalculation();

    // Search setup (called with IDs from pages that need it)
    setupSearch('searchInput', 'mainTable');
});

// ═══════════════════════════════════════════════════════════════
// CSV DRAG & DROP UPLOAD ENHANCEMENT
// ═══════════════════════════════════════════════════════════════
(function () {
  // Find all drop zones and wire them up
  document.querySelectorAll('.csv-upload-area').forEach(function (zone) {
    var fileInput  = zone.querySelector('input[type="file"]');
    var hintEl     = zone.querySelector('.csv-upload-hint');
    if (!fileInput) return;

    // Click anywhere in zone opens file picker
    zone.addEventListener('click', function (e) {
      if (e.target.classList.contains('csv-browse-link')) return; // handled inline
      fileInput.click();
    });

    // Show file name when chosen via input
    fileInput.addEventListener('change', function () {
      if (this.files.length > 0) {
        updateZone(zone, hintEl, this.files[0].name);
      }
    });

    // Drag events
    zone.addEventListener('dragover', function (e) {
      e.preventDefault();
      zone.classList.add('drag-over');
    });
    zone.addEventListener('dragleave', function () {
      zone.classList.remove('drag-over');
    });
    zone.addEventListener('drop', function (e) {
      e.preventDefault();
      zone.classList.remove('drag-over');
      var files = e.dataTransfer.files;
      if (files.length > 0) {
        var file = files[0];
        if (!file.name.endsWith('.csv')) {
          alert('Please drop a .csv file.');
          return;
        }
        // Transfer to file input via DataTransfer
        var dt = new DataTransfer();
        dt.items.add(file);
        fileInput.files = dt.files;
        updateZone(zone, hintEl, file.name);
      }
    });
  });

  function updateZone(zone, hintEl, filename) {
    zone.classList.add('has-file');
    zone.classList.remove('drag-over');
    if (hintEl) {
      hintEl.textContent = '📄 ' + filename + ' — ready to upload';
      hintEl.classList.add('selected');
    }
  }
})();
