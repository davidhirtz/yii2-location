import TomSelect from "tom-select";

type Option = {
    text: string;
    value: string | number;
};

export default (selector: string, url: string) => {
    new TomSelect(selector, {
        maxItems: 1,
        dropdownClass: 'dropdown-menu',
        optionClass: 'dropdown-option',
        load: (value: string, callback: Function) => {
            const input = new URL(url, window.location.origin);
            input.searchParams.set("q", value);

            fetch(input.toString())
                .then(response => response.json())
                .then(json => callback(json as Option[]))
                .catch(() => callback());

        },
        shouldLoad: (query: string) => query.length > 4,
        onChange: function (value: string) {
            this.input.value = value;
            console.log("Selected value:", this.input.value);
            // this.control_input.value = value;
        },
        onItemAdd: function (value: string, $item: HTMLElement) {
            $item.innerText = value;
        }
    });
};