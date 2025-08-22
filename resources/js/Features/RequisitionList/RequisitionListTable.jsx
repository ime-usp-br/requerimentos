import React from 'react';
import { useMemo, useState, useEffect } from 'react';
import { MaterialReactTable, useMaterialReactTable, MRT_ToggleFiltersButton } from 'material-react-table';

import { Link, Box, ButtonBase, TextField, InputAdornment, Divider, Stack, Grid2, IconButton, styled } from '@mui/material';
import SearchIcon from '@mui/icons-material/Search';
import OpenInNewIcon from '@mui/icons-material/OpenInNew';
import DeleteOutlinedIcon from '@mui/icons-material/DeleteOutlined';

import Builder from '../../ui/ComponentBuilder/Builder';
import columnTypes from "../../ui/ComponentBuilder/TableColumnTypes";

/**
 * Enumerables
 */
const COLOR = {
    ORANGE: '#FF9305',
    GREEN: '#0BC294',
    PURPLE: '#586EFF',
};

const PREFILTER = {
   OPEN: 'Abertos',
   SENT: '1 - SG',
   DEPARTMENT: '2 - Dep',
   REVIEW: '3 - Par',
   REGISTERED: '4 - Cad',
   CLOSED: '5 - Fech',
   ALL: 'Todos'
};

const PREFILTER_PRED = {
   OPEN: (req) => !(req.internal_status.includes("Requerimento") && !req.internal_status.includes("reavaliação")),
   SENT: (req) => req.internal_status.includes("Encaminhado"),
   DEPARTMENT: (req) => req.internal_status.toLowerCase().includes("departamento"),
   REVIEW: (req) => req.internal_status.toLowerCase().includes("parecer"),
   REGISTERED: (req) => req.internal_status.toLowerCase().includes("registrado") || req.internal_status.includes("automaticamente"),
   CLOSED: (req) => req.internal_status.includes("Requerimento") && !req.internal_status.includes("reavaliação"),
   ALL: () => true 
}

/**
 * Components
 */ 
const PreFilterButton = styled(ButtonBase)(({ backgroundcolor, selected, children, theme }) => ({
    align: 'center',
    backgroundColor: backgroundcolor || COLOR.ORANGE,
    paddingBlock: 4,
    paddingBottom: selected == children ? 14 : 5,
    paddingTop: selected == children ? 6 : 5,
    color: 'white',
    fontWeight: 600,
    fontSize: 18,
    [theme.breakpoints.up('md')]: {
        width: '70px',
    },
    [theme.breakpoints.up('lg')]: {
        width: '100px',
    },
    height: selected == children ? 50 : 35,
    filter: selected == children ? 'opacity(1)' : 'opacity(.6)',
    overflowX: 'hidden'
}));
PreFilterButton.defaultProps = {
    disableRipple: true
};

const builder = new Builder(columnTypes);

/**
 * Main component
 */
function List({ requisitions, selectedColumns }) {
    const [selectedPreFilter, setSelectedPreFilter] = useState('Abertos');
    const handlePrefilterClick = (event) => {
        const id = event.target.id;
        setSelectedPreFilter(PREFILTER[id]);
        setPrefilterPred(() => PREFILTER_PRED[id])
    };

    let textStyle = {
        sx: {
            fontSize: 20,
        },
    };
    let columns = useMemo(
        () => builder.build(selectedColumns),
        [selectedColumns],
    );

    const [columnFilters, setColumnFilters] = useState(() => {
        return JSON.parse(sessionStorage.getItem('filters')) || [];
    });
    useEffect(() => {
        sessionStorage.setItem('filters', JSON.stringify(columnFilters));
    }, [columnFilters]);


    const [globalFilter, setGlobalFilter] = useState(() => {
        return sessionStorage.getItem('globalFilter') || '';
    });

    const handleInputChange = (event) => {
        const text = event.target.value;
        setGlobalFilter(text);
        sessionStorage.setItem('globalFilter', text);
    };

    const [data, setData] = useState(requisitions);
    const [prefilterPred, setPrefilterPred] = useState(() => PREFILTER_PRED.OPEN);
    useEffect(() => {
        console.log(requisitions, prefilterPred);
        setData(requisitions.filter(prefilterPred));
    }, [requisitions, prefilterPred]);

    const table = useMaterialReactTable({
        columns,
        data,
        enableSorting: true,
        enableDensityToggle: false,
        enableFullScreenToggle: false,
        enableHiding: false,          // This disables column hiding functionality
        enableColumnDragging: false,
        enableFilters: true,
        enableColumnFilters: true,
        enableTopToolbar: true,
        enableColumnOrdering: false,
        enableGlobalFilter: true,
        enableRowActions: true,
        positionActionsColumn: 'last',
        muiPaginationProps: {
            color: 'primary',
            shape: 'rounded',
            showRowsPerPage: false,
            // variant: 'outlined',
        },
        paginationDisplayMode: 'pages',

        muiTableBodyCellProps: textStyle,
        displayColumnDefOptions: {
            'mrt-row-actions': {
                header: null,
                size: 30,
            },
        },
        defaultColumn: {
            minSize: 10
        },
        tableLayout: 'fixed',
        muiTableHeadRowProps: () => ({
            sx: {
                // backgroundColor: '#7CB4FD',
                backgroundColor: '#7CB4FD',
                color: 'white',
                height: 40,
                justifyContent: 'center'
            }
        }),
        muiTableHeadCellProps: () => ({
            sx: {
                fontSize: 20,
                color: 'white',
                // paddingTop: 10
            }
        }),
        muiTableBodyRowProps: ({ row }) => ({
            sx: {
                backgroundColor: row.index % 2 != 0 ? '#E3FAFF' : '#ffffff', // alternate colors
            },
        }),
        muiTablePaperProps: {
            elevation: 0,
            sx: {
                borderRadius: 0
            }
        },
        renderRowActions: ({ row }) => (
            <Box display="flex" alignItems="center" justifyContent="center" height="100%">
                <Link href={route('showRequisition', { requisitionId: row.original.id })} underline='never' color='textDisabled' display="flex" alignItems="center" justifyContent="center">
                    <OpenInNewIcon fontSize="medium" />
                </Link>
            </Box>
        ),
        state: { 
            columnFilters,
            globalFilter,
            density: 'compact',
        },
        onGlobalFilterChange: setGlobalFilter,
        onColumnFiltersChange: setColumnFilters,
        
        // Updated toolbar with functioning search box
        renderTopToolbar: ({ table }) => (
            <Stack
                sx={{
                    display: 'flex',
                    gap: '0.5rem',
                    pb: '8px',
                    justifyContent: 'flex-start',
                    alignItems: 'center',
                }}
            >
                {/* Global filter textbox */}
                <Grid2
                    container
                    direction='row'
                    sx={{
                        width: '100%',
                        marginTop: -6,
                        marginRight: 4,
                        // marginLeft: 46,
                        justifyContent: "flex-end",
                        alignItems: 'center',
                        position: 'fixed',
                        zIndex: 20
                    }}
                >
                    <MRT_ToggleFiltersButton 
                        table={table}
                        size='large'
                    />

                    <IconButton 
                        aria-label="clean"
                        size="large"
                        onClick={() => {
                            setColumnFilters([]);
                            setGlobalFilter('');
                        }}
                    >
                        <DeleteOutlinedIcon /> 
                    </IconButton>

                    <TextField
                        placeholder="Buscar por tudo..."
                        value={globalFilter ?? ''}
                        onChange={handleInputChange}
                        size="large"
                        variant="standard"
                        InputProps={{
                            startAdornment: (
                                <InputAdornment position="start">
                                    <SearchIcon />
                                </InputAdornment>
                            ),
                        }}
                        sx={{ 
                            width: '250px',
                            marginLeft: 1
                        }}
                    />
                </Grid2>
                
                <Grid2
                    container
                    direction='row'
                    spacing={{
                        md: .6,
                        lg: 2,
                    }}
                    sx={{
                        width: '100%',
                        height: '36px',
                        marginTop: -5.8,
                        marginRight: 96,
                        // marginLeft: 46,
                        justifyContent: "flex-end",
                        alignItems: 'center',
                        position: 'fixed',
                        zIndex: 20
                    }}
                >
                    <PreFilterButton
                        id='OPEN'
                        selected={selectedPreFilter}
                        onClick={handlePrefilterClick}
                    >
                        {PREFILTER.OPEN}
                    </PreFilterButton>
                    <PreFilterButton
                        id='SENT'
                        selected={selectedPreFilter}
                        onClick={handlePrefilterClick}
                    >
                        {PREFILTER.SENT}
                    </PreFilterButton>
                    <PreFilterButton
                        id='DEPARTMENT'
                        selected={selectedPreFilter}
                        onClick={handlePrefilterClick}
                    >
                        {PREFILTER.DEPARTMENT}
                    </PreFilterButton>
                    <PreFilterButton
                        id='REVIEW'
                        selected={selectedPreFilter}
                        onClick={handlePrefilterClick}
                    >
                        {PREFILTER.REVIEW}
                    </PreFilterButton>
                    <PreFilterButton
                        id='REGISTERED'
                        selected={selectedPreFilter}
                        onClick={handlePrefilterClick}
                    >
                        {PREFILTER.REGISTERED}
                    </PreFilterButton>
                    <PreFilterButton
                        id='CLOSED'
                        backgroundcolor={COLOR.GREEN}
                        selected={selectedPreFilter}
                        onClick={handlePrefilterClick}
                    >
                        {PREFILTER.CLOSED}
                    </PreFilterButton>
                    <PreFilterButton
                        id='ALL'
                        backgroundcolor={COLOR.PURPLE}
                        selected={selectedPreFilter}
                        onClick={handlePrefilterClick}
                    >
                        {PREFILTER.ALL}
                    </PreFilterButton>
                </Grid2>
                
                <Divider 
                    orientation='horizontal' 
                    flexItem 
                    sx={{ 
                        borderWidth: 5.5,
                        borderColor: ((prefilter) => {
                            switch (prefilter) {
                                case PREFILTER.CLOSED:
                                    return COLOR.GREEN;
                                case PREFILTER.ALL:
                                    return COLOR.PURPLE;
                                default:
                                    return COLOR.ORANGE;
                            }
                        })(selectedPreFilter),
                        // transition: 'all .6s'
                    }} 
                />
            </Stack>
        ),
        enableToolbarInternalActions: false,
    });

    return (
        <Box
            sx={{
                width: '100%',
                paddingX: 2,
                boxSizing: 'border-box'
            }}
        >
            <MaterialReactTable table={table} />
        </Box>
    );
};

export default List;