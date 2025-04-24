import React, { useMemo, useState } from "react";
import { Button, Typography, Paper, Stack, TextField } from "@mui/material";
import { useForm } from "@inertiajs/react";
import {
    MaterialReactTable,
    useMaterialReactTable,
} from "material-react-table";
import RemoveRoleConfirmationDialog from "../Dialogs/RemoveRoleConfirmationDialog";
import { useDialogContext } from '../Context/useDialogContext';
import axios from "axios";


function ManageUsers({ users }) {
    const [globalFilter, setGlobalFilter] = useState("");

    const { setDialogTitle, setDialogBody, openDialog, closeDialog } = useDialogContext();

    function handleRemoveRole(name, nusp, role) {
        const updatedData = { name, nusp, role };
    
        setDialogTitle("Confirmação de remoção de papel");
        setDialogBody(
            <RemoveRoleConfirmationDialog
                removeRole={() => removeRole(updatedData)}
                data={updatedData}
            />
        );
        openDialog();
    }
    
    async function removeRole(updatedData) {
        setUsersData((prevData) =>
            prevData.filter(
                (user) =>
                    !(user.codpes === updatedData.nusp && user.role == updatedData.role)
            )
        );
    
        try {
            await axios.post(route("role.remove"), updatedData);
        } catch (error) {
            console.error("Error:", error.response?.data || error.message);
        }
    }

    const [usersData, setUsersData] = useState(() =>
        users.flatMap((user) =>
            user.roles.map((role) => ({
                id: user.id,
                name: user.name,
                codpes: user.codpes,
                role: role.name,
            }))
        )
    );

    const columns = useMemo(() => [
        { accessorKey: "codpes", header: "Número USP", size: 50 },
        {
            accessorKey: "name",
            header: "Nome",
            size: 100,
            Cell: ({ cell }) =>
                cell.getValue() ?? "Desconhecido (usuário nunca logou no site)",
        },
        {
            accessorKey: "role",
            header: "Papel",
            filterVariant: "multi-select",
            size: 200,
        },
        {
            accessorFn: (row) => `remove-${row.codpes}-${row.role}`,
            id: "remove",
            header: "",
            size: 0,
            enableSorting: false,
            enableColumnActions: false,
            Cell: ({ row }) => (
                <Button
                    variant="contained"
                    color="error"
                    onClick={() =>
                        handleRemoveRole(
                            row.original.name,
                            row.original.codpes,
                            row.original.role
                        )
                    }
                >
                    Remover papel
                </Button>
            ),
        },
    ]);

    let headerStyle = {
        sx: {
            fontSize: 18,
        },
    };

    let bodyStyle = {
        sx: {
            fontSize: 18,
        },
    };

    const table = useMaterialReactTable({
        columns,
        data: usersData,
        enableDensityToggle: false,
        enableFullScreenToggle: false,
        enableHiding: false,
        enableColumnDragging: false,
        enableTopToolbar: false,
        enableGlobalFilter: true,
        enableSorting: true,
        enableColumnFilters: true,
        muiTableHeadCellProps: headerStyle,
        muiTableBodyCellProps: bodyStyle,
        initialState: { density: "compact" },
        state: { globalFilter },
        onGlobalFilterChange: setGlobalFilter,
    });

    return (
        <Stack spacing={2}>
            <Typography variant="h5">Usuários cadastrados</Typography>
            <TextField
                label="Pesquisar"
                variant="outlined"
                size="small"
                value={globalFilter ?? ""}
                onChange={(e) => setGlobalFilter(e.target.value)}
                sx={{ mb: 2 }}
            />
            <Paper>
                <MaterialReactTable table={table} />
            </Paper>
        </Stack>
    );
};

export default ManageUsers;
