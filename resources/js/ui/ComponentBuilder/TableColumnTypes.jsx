import React from "react";
import { Typography } from "@mui/material";

let columnTypes = {};

columnTypes.id = {
    header: <Typography 
                variant="h7" 
                align="center" 
            >
                <b>ID</b>
            </Typography>,
    accessorKey: 'id',
    enableHiding: false,
    size: 30
};

columnTypes.created_at = {
    header: <Typography 
                variant="h7" 
                align="center" 
            >
                <b>Criado em</b>
            </Typography>,
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
    header: <Typography 
                variant="h7" 
                align="center"
            >
                <b>Últ. mod.</b>
            </Typography>,
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
    header: <Typography 
                variant="h7" 
                align="center"
            >
                <b>Disciplina requerida</b>
            </Typography>,
    accessorKey: 'requested_disc',
    enableHiding: false,
    size: 0
};

columnTypes.situation = {
    header: <Typography 
                variant="h7" 
                align="center"
            >
                <b>Situação</b>
            </Typography>,
    accessorKey: 'situation',
    enableHiding: false,
    Cell: ({ renderedCellValue }) => (
        <Typography 
            variant="h7"
            style={{ wordBreak: 'break-word' }}
        >
            {renderedCellValue}
        </Typography>
    )
};

columnTypes.internal_status = {
    header: <Typography 
                variant="h7" 
                align="center"
            >
                <b>Situação</b>
            </Typography>,
    accessorKey: 'internal_status',
    enableHiding: false,
    Cell: ({ renderedCellValue }) => (
        <Typography 
            variant="h7"
            width={250}
            style={{ wordBreak: 'break-word', whiteSpace: "pre-line" }}
        >
            {renderedCellValue}
        </Typography>
    )
};

columnTypes.student_name = {
    header: <Typography 
                variant="h7" 
                align="center"
            >
                <b>Aluno</b>
            </Typography>,
    accessorKey: 'student_name',
    enableHiding: false,
    size: 120
};

columnTypes.student_nusp = {
    header: <Typography 
                variant="h7" 
                align="center"
            >
                <b>NUSP</b>
            </Typography>,
    accessorKey: 'student_nusp',
    enableHiding: false,
    size: 0
};

columnTypes.department = {
    header: <Typography 
                variant="h7" 
                align="center"
            >
                <b>Departamento</b>
            </Typography>,
    accessorKey: 'department',
    enableHiding: false,
    size: 160
};

columnTypes.reviewer_decision = {
    header: <Typography 
                variant="h7" 
                align="center"
            >
                <b>Decisão</b>
            </Typography>,
    accessorKey: 'reviewer_decision',
    enableHiding: false,
    size: 160
};

columnTypes.type = {
    header: <Typography 
                variant="h7" 
                align="center"
            >
                <b>Tipo</b>
            </Typography>,
    accessorKey: 'type',
    enableHiding: false,
    size: 400
};

columnTypes.author_name = {
    header: <Typography 
                variant="h7" 
                align="center"
            >
                <b>Autor</b>
            </Typography>,
    accessorKey: 'author_name',
    enableHiding: false,
    size: 120
};

columnTypes.author_nusp = {
    header: <Typography 
                variant="h7" 
                align="center"
            >
                <b>Número USP</b>
            </Typography>,
    accessorKey: 'author_nusp',
    enableHiding: false,
    size: 0
};

columnTypes.ocurrence_time  = {
    header: <Typography 
                variant="h7" 
                align="center"
            >
                <b>Horário de<br /> ocorrência</b>
            </Typography>,
    accessorFn: (row) => row.created_at.slice(11, 19),
    enableHiding: false,
    size: 0
};

export default columnTypes;