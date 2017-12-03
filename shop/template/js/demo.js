function displayResult(item, val, text) {
    console.log(item);
    $('.alert').show().html('You selected <strong>' + val + '</strong>: <strong>' + text + '</strong>');
}

$(function () {
    $('#search').typeahead({
        source: [
		    { ID: 1, Name: '笑答' },
		    { ID: 2, Name: '江南' },
		    { ID: 3, Name: '多发' },
		    { ID: 4, Name: '多啊啊啊' },
		    { ID: 5, Name: 'Boston' },
		    { ID: 6, Name: 'Columbus' },
		    { ID: 7, Name: 'Dallas' },
		    { ID: 8, Name: 'Vancouver' },
		    { ID: 9, Name: 'Seattle' },
		    { ID: 10, Name: 'Los Angeles' }
	    ],
        display: 'Name',
        val: 'ID',
        itemSelected: displayResult
    });

    // Mock an AJAX request
    $.mockjax({
        url: '/cities/list',
        responseText: [{ id: 1, name: 'Toronto' },
				    { id: 2, name: 'Mbbbbbbbb' },
				    { id: 3, name: 'New York' },
				    { id: 4, name: 'Buffalo' },
				    { id: 5, name: 'Boston' },
				    { id: 6, name: 'Columbus' },
				    { id: 7, name: 'Dallas' },
				    { id: 8, name: 'Vancouver' },
				    { id: 9, name: 'Seattle' },
				    { id: 10, name: 'Los Angeles' }]
    });

    $('#demo4').typeahead({
        ajax: '/cities/list',        
        itemSelected: displayResult
    });

});