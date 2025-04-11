class Builder {
    constructor(reference) {
        this.reference = reference;
    }

    build(selectedColumns) {
        this.structure = [];
        for (let attribute of selectedColumns) {
            if (this.reference[attribute] == null) {
                this.structure = null;
                break;
            }
            this.structure.push(this.reference[attribute]);
        }
        return this.structure;
    }
}

export default Builder;