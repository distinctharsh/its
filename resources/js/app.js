import './bootstrap';

import $ from 'jquery';  // Import jQuery
import 'select2/dist/css/select2.min.css';
import 'select2';

// Initialize Select2
$(document).ready(function() {
    const select2Options = {
        allowClear: true,
        minimumResultsForSearch: 0,
        closeOnSelect: false
    };

    $('#nationality').select2({
        ...select2Options,
        placeholder: "Select Country"
    });

    $('#inspector_id').select2({
        ...select2Options,
        placeholder: "Select Inspector"
    });

    $('#edit_inspector_id').select2({
        ...select2Options,
        placeholder: "Select Inspector"
    });

    $('#edit_team_lead').select2({
        ...select2Options,
        placeholder: "Select Inspector"
    });

    $('#rank').select2({
        ...select2Options,
        placeholder: "Select Inspector"
    });
    $('#team_lead').select2({
        ...select2Options,
        placeholder: "Select Inspector"
    });
    

    $('#list_of_inspectors').select2({
        ...select2Options,
        placeholder: "Select Inspectors",
        tags: false // Disable tagging
    });

    $('#edit_list_of_inspectors').select2({
        ...select2Options,
        placeholder: "Select Inspectors",
        
        tags: false // Disable tagging
    });

    $('#escort_officers').select2({
        ...select2Options,
        placeholder: "Select Escort Officer",
        tags: false // Disable tagging
    });

    $('#edit_list_of_escort_officers').select2({
        ...select2Options,
        placeholder: "Select Escort Officer",
        // templateResult: formatOption,
        // templateSelection: formatSelection,
        tags: false // Disable tagging
    });


 

    $('select[name="state_id[]"]').select2({...select2Options, placeholder: "Search State"});
    $('#state_id').select2({...select2Options, placeholder: "Search State"});

    $('select[name="site_code_id[]"]').select2({
        ...select2Options,
        placeholder: "Search Site Code"
    });

    function formatOption(option) {
        if (!option.id) {
            return option.text; 
        }

        return $(
            `<span>
                <input type="checkbox" class="select2-checkbox" ${option.selected ? 'checked' : ''} /> ${option.text}
            </span>`
        );
    }

    function formatSelection(selection) {
        return selection.text; 
    }
});
