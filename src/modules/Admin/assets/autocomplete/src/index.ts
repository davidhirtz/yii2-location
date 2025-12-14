const $input = $('#autocomplete');

$input.autocomplete({
    appendTo: $input.parent(),
    minLength: 5,
    source: '/admin/location/autocomplete',
});