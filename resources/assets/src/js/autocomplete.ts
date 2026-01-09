import TomSelect from "tom-select";

type Option = {
    label: string;
    value: string | number;
};

export default (selector: string, url: string) => {
    new TomSelect(selector, {
        labelField: "label",
        searchField: ["label"],
        load: (value: string, callback: Function) => {
            const input = new URL(url, window.location.origin);
            input.searchParams.set("q", value);
            console.log(input.toString());

            fetch(input.toString())
                .then(response => response.json())
                .then(json => callback(json))
                .catch(() => callback());

        },
        shouldLoad: (query: string) => query.length > 4,
        // render: {
        //     option: function (item, escape) {
        //         return `<div class="dropdown-item"><div class="dropdown-link">${escape(item.label)}</div></div>`;
        //     },
        // },
    });
};