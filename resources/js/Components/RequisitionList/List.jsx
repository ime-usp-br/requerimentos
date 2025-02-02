import React from 'react';
import { useMemo } from 'react';
import { MaterialReactTable, useMaterialReactTable } from 'material-react-table';
import VisibilityIcon from '@mui/icons-material/Visibility';
import { Box, Link } from '@mui/material';

import Builder from './columnBuilder';

export default function List({ requisitions, selectedColumns }) {
    // let columnTypes = {};
    // // 1: aluno
    // columnTypes[1] = useMemo(
    //     () => [
    //         {
    //             header: 'ID',
    //             accessorKey: 'id',
    //             enableHiding: false,
    //             size: 40
    //         },
    //         {
    //             header: 'Data de criação',
    //             accessorFn: (row) => row.created_at.slice(0, 10),
    //             enableHiding: false,
    //             size: 180
    //         },
    //         {
    //             header: 'Disciplina requerida',
    //             accessorKey: 'requested_disc',
    //             enableHiding: false,
    //             size: 360
    //         },
    //         {
    //             header: 'Situação',
    //             accessorKey: 'situation',
    //             enableHiding: false,
    //             size: 700,
    //             grow: true
    //         },
    //     ],
    //     [],
    // );
    // // 2: SG
    // columnTypes[2] = useMemo(
    //     () => [
    //         {
    //             header: 'Data de criação',
    //             accessorFn: (row) => row.created_at.slice(0, 10),
    //             enableHiding: false,
    //             enableColumnActions: false,
    //             size: 100
    //         },
    //         {
    //             header: 'ID',
    //             accessorKey: 'id',
    //             enableHiding: false,
    //             size: 40
    //         },
    //         {
    //             header: 'Aluno',
    //             accessorKey: 'student_name',
    //             enableHiding: false,
    //             size: 120
    //         },
    //         {
    //             header: 'Número USP',
    //             accessorKey: 'student_nusp',
    //             enableHiding: false,
    //             size: 60
    //         },
    //         {
    //             header: 'Situação',
    //             accessorKey: 'internal_status',
    //             enableHiding: false,
    //             size: 600
    //         },
    //         {
    //             header: 'Departamento',
    //             accessorKey: 'department',
    //             enableHiding: false,
    //             size: 260
    //         },
    //     ],
    //     [],
    // );
    // // 4: Departamentos
    // // Dep: id, criação, ultima modificação, aluno, nusp, situação
    // columnTypes[4] = useMemo(
    //     () => [
    //         {
    //             header: 'ID',
    //             accessorKey: 'id',
    //             enableHiding: false,
    //             size: 40
    //         },
    //         {
    //             header: 'Data de criação',
    //             accessorFn: (row) => row.created_at.slice(0, 10),
    //             enableHiding: false,
    //             enableColumnActions: false,
    //             size: 100
    //         },
    //         {
    //             header: 'Última modificação',
    //             accessorFn: (row) => row.updated_at.slice(0, 10),
    //             enableHiding: false,
    //             enableColumnActions: false,
    //             size: 100
    //         },
    //         {
    //             header: 'Aluno',
    //             accessorKey: 'student_name',
    //             enableHiding: false,
    //             size: 120
    //         },
    //         {
    //             header: 'Número USP',
    //             accessorKey: 'student_nusp',
    //             enableHiding: false,
    //             size: 60
    //         },
    //         {
    //             header: 'Situação',
    //             accessorKey: 'internal_status',
    //             enableHiding: false,
    //             size: 600
    //         }
    //     ],
    //     [],
    // );

    let builder = new Builder(selectedColumns);
    console.log(builder.getStructure());
    let columns = useMemo(
        () => builder.getStructure(),
        [],
    );
    let data = requisitions;
    const table = useMaterialReactTable({
        columns,
        data,
        rowCount: 20,
        enableSorting: false,
        enableDensityToggle: false,
        enableFullScreenToggle: false,
        enableHiding: false,
        enableColumnDragging: false,
        enableFilters: true,
        enableColumnFilters: true,
        enableTopToolbar: false,
        enableColumnOrdering: true,
        enableGlobalFilter: false,
        enableRowActions: true,
        displayColumnDefOptions: {
            'mrt-row-actions': {
                header: null, //change header text
                size: 80, //make actions column wider
            },
        },
        renderRowActions: ({ row }) => (
            <Link href={"/aluno/detalhe/" + row.original.id} underline='never' color='textDisabled'>
                <VisibilityIcon />
            </Link>
        ),
        initialState: { density: 'compact' },
    });

    return <MaterialReactTable table={table} />;
};