import React, { useState, useEffect, useMemo } from 'react';
import { Autocomplete, TextField, CircularProgress } from '@mui/material';

const debounce = (func, delay) => {
    let timeoutId;
    const debounced = (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(null, args), delay);
    };
    debounced.cancel = () => clearTimeout(timeoutId);
    return debounced;
};

const AsyncSubjectAutocomplete = ({
    value,
    onChange,
    disabled = false,
    error = false,
    helperText = '',
    label = "Código ou nome da disciplina",
    size = "small",
    required = false
}) => {
    const [inputValue, setInputValue] = useState('');
    const [options, setOptions] = useState([]);
    const [loading, setLoading] = useState(false);
    const [open, setOpen] = useState(false);

    // Debounced search function
    const debouncedSearch = useMemo(
        () => debounce(async (searchQuery) => {
            if (searchQuery.length < 2) {
                setOptions([]);
                setLoading(false);
                return;
            }

            setLoading(true);
            try {
                const response = await fetch(`/api/subjects/search?q=${encodeURIComponent(searchQuery)}`);
                if (response.ok) {
                    const data = await response.json();
                    setOptions(data);
                } else {
                    console.error('Failed to fetch subjects:', response.statusText);
                    setOptions([]);
                }
            } catch (error) {
                console.error('Error fetching subjects:', error);
                setOptions([]);
            } finally {
                setLoading(false);
            }
        }, 300),
        []
    );

    // Effect to trigger search when inputValue changes
    useEffect(() => {
        if (open) {
            debouncedSearch(inputValue);
        }

        // Cleanup function to cancel pending debounced calls
        return () => {
            debouncedSearch.cancel();
        };
    }, [inputValue, open, debouncedSearch]);

    // Clear options when component unmounts or closes
    useEffect(() => {
        if (!open) {
            setOptions([]);
        }
    }, [open]);

    return (
        <Autocomplete
            size={size}
            open={open}
            onOpen={() => setOpen(true)}
            onClose={() => setOpen(false)}
            value={value || null}
            onChange={(event, newValue) => {
                onChange(newValue);
            }}
            inputValue={inputValue}
            onInputChange={(event, newInputValue, reason) => {
                setInputValue(newInputValue);
            }}
            options={options}
            getOptionLabel={(option) => {
                if (typeof option === 'string') {
                    return option;
                }
                return option?.code || '';
            }}
            renderOption={(props, option) => (
                <li {...props} key={option.code}>
                    {option.label}
                </li>
            )}
            loading={loading}
            loadingText="Buscando disciplinas..."
            noOptionsText={inputValue.length < 2 ? "Digite pelo menos 2 caracteres" : "Nenhuma disciplina encontrada"}
            filterOptions={(x) => x} // Disable client-side filtering since we filter on server
            isOptionEqualToValue={(option, value) => {
                if (!option || !value) return false;
                if (typeof value === 'string') {
                    return option.code === value;
                }
                return option.code === value.code;
            }}
            disabled={disabled}
            fullWidth
            renderInput={(params) => (
                <TextField
                    {...params}
                    label={label}
                    variant="outlined"
                    required={required}
                    error={error}
                    helperText={helperText}
                    slotProps={{
                        input: {
                            ...params.InputProps,
                            endAdornment: (
                                <>
                                    {loading ? <CircularProgress color="inherit" size={20} /> : null}
                                    {params.InputProps.endAdornment}
                                </>
                            ),
                        },
                    }}
                />
            )}
        />
    );
};

export default AsyncSubjectAutocomplete;
