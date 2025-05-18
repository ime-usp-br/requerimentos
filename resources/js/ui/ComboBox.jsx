import React from "react";
import { Autocomplete, TextField } from "@mui/material";

export default function ComboBox({ size, options, sx, name, optionGetter, defaultValue, value, onChange, ...props }) {
    // Use value prop if provided, otherwise fall back to defaultValue
    const autoCompleteProps = value !== undefined ? { value } : { defaultValue };
    
    return (
        <Autocomplete 
            size={size}
            disablePortal
            options={options}
            getOptionLabel={optionGetter}
            sx={sx}
            renderInput={(params) => (
                <TextField 
                    {...params}
                    label={name} 
                />
            )}
            onChange={(_, newValue) => onChange(newValue)}
            {...autoCompleteProps}
            {...props}
        />
    );
};
