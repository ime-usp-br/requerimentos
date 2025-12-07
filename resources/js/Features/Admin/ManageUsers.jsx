import React, { useMemo, useState } from "react";
import { Button, Typography, Paper, Stack, TextField } from "@mui/material";
import {
    MaterialReactTable,
    useMaterialReactTable,
} from "material-react-table";
import RemoveRoleConfirmationDialog from "./RemoveRoleConfirmationDialog";
import { useDialogContext } from '../../Context/useDialogContext';
import axios from "axios";


function ManageUsers({ users }) {
    const [globalFilter, setGlobalFilter] = useState("");

    const { setDialogTitle, setDialogBody, openDialog } = useDialogContext();

    const [usersData, setUsersData] = useState(() =>
        users.map((user) => ({
            name: user.name,
            nusp: user.nusp,
            roleName: user.roleName,
            roleId: user.roleId,
            departmentName: user.departmentName,
            departmentId: user.departmentId
        }))
    )

    function handleRemoveRole(userRole) {
        setDialogTitle("Confirmação de remoção de papel");
        setDialogBody(
            <RemoveRoleConfirmationDialog
                userRole={userRole}
                removeRole={() => removeRole(userRole)}
            />
        );
        openDialog();
    }

    async function removeRole(userRole) {
        setUsersData((prevData) =>
            prevData.filter(
                (user) =>
                    !(user.nusp === userRole.nusp
                        && user.roleId == userRole.roleId
                        && user.departmentId == userRole.departmentId)
            )
        );

        try {
            await axios.post(route("role.remove"), {nusp: userRole.nusp,
                                                    roleId: userRole.roleId,
                                                    departmentId: userRole.departmentId});
        } catch (error) {
            console.error("Error:", error.response?.data || error.message);
        }
    }

    const columns = useMemo(() => [
        { accessorKey: "nusp", header: "Número USP", size: 50 },
        {
            accessorKey: "name",
            header: "Nome",
            size: 100,
            Cell: ({ cell }) =>
                cell.getValue() ?? "Desconhecido (usuário nunca logou no site)",
        },
        {
            accessorKey: "roleName",
            header: "Papel",
            filterVariant: "multi-select",
            size: 200,
        },
        {
            accessorKey: "departmentName",
            header: "Departamento",
            filterVariant: "multi-select",
            size: 200,
        },
        {
            accessorFn: (row) => `remove-${row.nusp}-${row.role}`,
            id: "remove",
            header: "",
            size: 0,
            enableSorting: false,
            enableColumnActions: false,
            Cell: ({ row }) => (
                <Button
                    variant="contained"
                    color="error"
                    onClick={() => handleRemoveRole(row.original)}
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
        <Stack
            direction="column"
            spacing={2}
            sx={{
                width: 'calc(100vw - 32px)',
                justifyContent: 'flex-start',
                mt: 2,
                paddingX: 2
            }}
        >
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
