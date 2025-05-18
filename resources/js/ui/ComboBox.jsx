import React from "react";
import { Autocomplete, TextField } from "@mui/material";

export default function ComboBox({ size, options, sx, name, optionGetter, defaultValue, onChange, ...props }) {
    return (
        <Autocomplete 
            size={size}
            disablePortal
            options={options}
            defaultValue={defaultValue}
            getOptionLabel={optionGetter}
            sx={sx}
            renderInput={(params) => (
                <TextField 
                    {...params}
                    label={name} 
                />
            )}
            onChange={(_, value) => onChange(value)}
            {...props}
        />
    );
};
