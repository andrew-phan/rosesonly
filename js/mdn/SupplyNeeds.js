//****************************************************************************************
//Create a new purchase order from selected products in supply needs
function createPurchaseOrder()
{
    //check that a supplier is selected
    var supId = document.getElementById('sup_id').selectedIndex;
    if (supId == 0)
    {
        alert('Please select a supplier !');
        return false;
    }

    //save changes in hidden field
    persistantGrid.storeLogInTargetInput();

    //submit form
    document.getElementById('frm_add_to_po').submit();
    
}

//*********************************************************************************************
//Import supply needs in current purchase order
function importSupplyNeeds()
{
    //save changes in hidden field
    persistantGrid.storeLogInTargetInput();
    var selectedValues = document.getElementById('supply_needs_log').value;
    if (selectedValues == '')
    {
        alert('No products are selected !');
        return false;
    }

    window.opener.document.getElementById('supply_needs_ids').value = selectedValues;

    //submit parent form
    window.opener.document.getElementById('edit_form').submit();

    //close window
    self.close();
}

function applyWarehouse(url)
{
    var warehouseId = document.getElementById('warehouse').value;
    url = url.replace('XXX', warehouseId);
    document.location.href = url;
}