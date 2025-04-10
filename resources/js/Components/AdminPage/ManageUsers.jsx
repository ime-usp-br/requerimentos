import React, { useMemo } from "react";
import { Button, Typography, Paper, Stack, TextField } from "@mui/material";
import { useForm } from "@inertiajs/react";
import {
    MaterialReactTable,
    useMaterialReactTable,
} from "material-react-table";
import RemoveRoleConfirmationDialog from "./Dialogs/RemoveRoleConfirmationDialog";

const ManageUsers = ({ users }) => {
    const [globalFilter, setGlobalFilter] = React.useState("");

    const [confirmationOpen, setOpenConfirmation] = React.useState(false);
    const handleOpenConfirmation = () => setOpenConfirmation(true);
    const handleCloseConfirmation = () => setOpenConfirmation(false);

    const handleRemoveRole = (name, nusp, role) => {
        setData("name", name);
        setData("nusp", nusp);
        setData("role", role);
        handleOpenConfirmation();
    };

    const removeRole = () => {
        setUsersData((prevData) =>
            prevData.filter(
                (user) =>
                    !(user.codpes === data.nusp && user.role === data.role)
            )
        );
        post(route("role.remove"), {
            onSuccess: () => {},
        });

        handleCloseConfirmation();
    };

    const { data, setData, post } = useForm({
        name: "",
        nusp: "",
        role: "",
    });

    const [usersData, setUsersData] = React.useState(() =>
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
        //simple styling with the `sx` prop, works just like a style prop in this example
        sx: {
            fontSize: 18,
            // padding: 1
        },
    };

    let bodyStyle = {
        //simple styling with the `sx` prop, works just like a style prop in this example
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
            <RemoveRoleConfirmationDialog
                open={confirmationOpen}
                handleClose={handleCloseConfirmation}
                removeRole={removeRole}
                data={data}
            />
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
