import columnTypes from "./columnTypes";

class Builder {
    constructor(selectedColumns) {
        this.structure = [];
        for (let attribute of selectedColumns) {
            if (columnTypes[attribute] == null) {
                this.structure = null;
                break;
            }
            this.structure.push(columnTypes[attribute]);
        }
    }

    getStructure() {
        return this.structure;
    }
}

export default Builder;