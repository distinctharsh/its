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
        placeholder: "Select Nationality"
    });

    $('#inspector_id').select2({
        ...select2Options,
        placeholder: "Select Inspector"
    });

    $('#country').select2({
        ...select2Options,
        placeholder: "Select Nationality"
    });

    $('#state').select2({
        ...select2Options,
        placeholder: "Select State"
    });

    $('#issue').select2({
        ...select2Options,
        placeholder: "Select Inspection Issue"
    });

    $('#designation').select2({
        ...select2Options,
        placeholder: "Select Designation"
    });
    $('#rank').select2({
        ...select2Options,
        placeholder: "Select Rank"
    });
    $('#designationId').select2({
        ...select2Options,
        placeholder: "Select Designation"
    });
    $('#designation_id').select2({
        ...select2Options,
        placeholder: "Select Designation"
    });

    $('#typeOfInspection').select2({
        ...select2Options,
        placeholder: "Select Inspection Category"
    });

    $('#inspectionCategoryType').select2({
        ...select2Options,
        placeholder: "Select Sub Category Type"
    });

    $('#visitCategory').select2({
        ...select2Options,
        placeholder: "Select Visit Category"
    });

    $('#inspectionTypeSelection').select2({
        ...select2Options,
        placeholder: "Select Inspection Type"
    });

    $('#siteCode').select2({
        ...select2Options,
        placeholder: "Select Site Code"
    });

    $('#status').select2({
        ...select2Options,
        placeholder: "Select Status"
    });

    $('#edit_inspector_id').select2({
        ...select2Options,
        placeholder: "Select Inspector"
    });

    $('#edit_team_lead').select2({
        ...select2Options,
        placeholder: "Select Team Lead"
    });
    $('#escortOfficer').select2({
        ...select2Options,
        placeholder: "Select Escort Officer"
    });

 
    $('#team_lead').select2({
        ...select2Options,
        placeholder: "Select Team Lead"
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
    $('#escort_officers_poe').select2({
        ...select2Options,
        placeholder: "Select Escort Officer",
        tags: false // Disable tagging
    });
  
    $('#point_of_entry').select2({
        ...select2Options,
        placeholder: "Select Point of Entry",
        tags: false // Disable tagging
    });
    $('#point_of_exit').select2({
        ...select2Options,
        placeholder: "Select Point of Exit",
        tags: false // Disable tagging
    });
    $('#edit_point_of_entry').select2({
        ...select2Options,
        placeholder: "Select Point of Entry",
        tags: false // Disable tagging
    });
    $('#edit_point_of_exit').select2({
        ...select2Options,
        placeholder: "Select Point of Exit",
        tags: false // Disable tagging
    });

    $('#edit_list_of_escort_officers').select2({
        ...select2Options,
        placeholder: "Select Escort Officer",
        // templateResult: formatOption,
        // templateSelection: formatSelection,
        tags: false // Disable tagging
    });

 

    // $('select[name="state_id[]"]').select2({...select2Options, placeholder: "Search State"});
    // $('#state_id').select2({...select2Options, placeholder: "Search State"});

    // $('select[name="site_code_id[]"]').select2({
    //     ...select2Options,
    //     placeholder: "Search Site Code"
    // });

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
