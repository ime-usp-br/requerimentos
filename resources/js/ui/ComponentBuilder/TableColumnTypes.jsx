import React from "react";
// import { Typography } from "@mui/material";

let columnTypes = {};

columnTypes.id = {
    header: "ID",
    accessorKey: 'id',
    enableHiding: false,
    size: 0,
};

columnTypes.created_at = {
    header: "Criado em",
    accessorKey: 'created_at',
    sortingFn: 'datetime',
    Cell: ({ row }) => {
        const date = new Date(row.original.created_at);
        const pad = (n) => n.toString().padStart(2, '0');
        return `${pad(date.getDate())}-${pad(date.getMonth() + 1)}-${date.getFullYear()} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
    },
    enableHiding: false,
    size: 0
};

columnTypes.updated_at = {
    header: "Últ. mod.",
    accessorKey: 'updated_at',
    sortingFn: 'datetime',
    Cell: ({ row }) => {
        const date = new Date(row.original.updated_at);
        const pad = (n) => n.toString().padStart(2, '0');
        return `${pad(date.getDate())}-${pad(date.getMonth() + 1)}-${date.getFullYear()} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
    },
    enableHiding: false,
    size: 0
};

columnTypes.requested_disc = {
    header: "Pedido",
    accessorKey: 'requested_disc',
    enableHiding: false,
    size: 0
};

columnTypes.requested_disc_code = {
    header: "Pedido",
    accessorKey: 'requested_disc_code',
    enableHiding: false,
    size: 0
};

columnTypes.situation = {
    header: "Situação",
    accessorKey: 'situation',
    enableHiding: false,
    grow: true,
};

columnTypes.internal_status = {
    header: "Situação",
    accessorKey: 'situation',
    enableHiding: false,
    grow: true,
};

columnTypes.student_name = {
    header: "Aluno",
    accessorKey: 'student_name',
    enableHiding: false,
    size: 120
};

columnTypes.student_nusp = {
    header: "NUSP",
    accessorKey: 'student_nusp',
    enableHiding: false,
    size: 0
};

columnTypes.department = {
    header: "Departamento",
    accessorKey: 'department',
    enableHiding: false,
    size: 160
};

columnTypes.reviewer_decision = {
    header: "Decisão",
    accessorKey: 'reviewer_decision',
    enableHiding: false,
    size: 160
};

columnTypes.type = {
    header: "Tipo",
    accessorKey: 'type',
    enableHiding: false,
    size: 400
};

columnTypes.author_name = {
    header: "Autor",
    accessorKey: 'author_name',
    enableHiding: false,
    size: 120
};

columnTypes.author_nusp = {
    header: "Número USP",
    accessorKey: 'author_nusp',
    enableHiding: false,
    size: 0
};

columnTypes.ocurrence_time  = {
    header: "Horário de<br /> ocorrência",
    accessorFn: (row) => row.created_at.slice(11, 19),
    enableHiding: false,
    size: 0
};

export default columnTypes;