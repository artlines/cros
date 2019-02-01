import times from "lodash/times";

function createDevData(obj, count = 10, id = "id") {
    let items = [];

    times(count, i => {
        items.push({
            ...obj,
            [id]: Number(i) + 1,
        });
    });

    return items;
}

export default createDevData;