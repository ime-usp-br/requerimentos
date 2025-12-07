import React from 'react';
import FullTextTooltip from "../../Features/FullTextTooltip";

const fullTextTooltipWrapper = ({ cell }) => <FullTextTooltip value={cell.getValue()} fontSize={18} />;

const formatDate = ({ row }) => {
    const date = new Date(row.original.created_at);
    const pad = (n) => n.toString().padStart(2, '0');
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
};

let columnTypes = {};

columnTypes.id = {
    header: "ID",
    accessorKey: 'id',
    enableHiding: false,
    size: 10,
};

columnTypes.created_at = {
    header: "Criado em",
    accessorKey: 'created_at',
    sortingFn: 'datetime',
    Cell: formatDate,
    enableHiding: false,
    size: 10
};

columnTypes.updated_at = {
    header: "Últ. mod.",
    accessorKey: 'updated_at',
    sortingFn: 'datetime',
    Cell: formatDate,
    enableHiding: false,
    size: 10
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
    size: 0,
    muiTableBodyCellProps: {
        sx: {
            maxWidth: 80,
            overflow: 'hidden',
            whiteSpace: 'nowrap',
            textOverflow: 'ellipsis',
            fontSize: 18
        },
    },
};

columnTypes.situation = {
    header: "Situação",
    accessorKey: 'situation',
    enableHiding: false,
    size: 0
};

columnTypes.internal_status = {
    header: "Situação",
    accessorKey: 'situation',
    enableHiding: false,
    size: 0
};

columnTypes.student_name = {
    header: "Aluno",
    accessorKey: 'student_name',
    enableHiding: false,
    size: 120,
    muiTableBodyCellProps: {
        sx: {
            maxWidth: 120,
            overflow: 'hidden',
            whiteSpace: 'nowrap',
            textOverflow: 'ellipsis',
            fontSize: 18
        },
    },
    Cell: fullTextTooltipWrapper,
};

columnTypes.student_nusp = {
    header: "NUSP",
    accessorKey: 'student_nusp',
    enableHiding: false,
    size: 0
};

columnTypes.department = {
    header: "Depart.",
    accessorKey: 'department',
    enableHiding: false,
    size: 20,
    muiTableBodyCellProps: {
        sx: {
            maxWidth: 30,
            overflow: 'hidden',
            whiteSpace: 'nowrap',
            textOverflow: 'ellipsis',
            fontSize: 18
        },
    },
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

columnTypes.ocurrence_time = {
    header: "Horário de ocorrência",
    accessorFn: (row) => row.created_at.slice(11, 19),
    enableHiding: false,
    size: 0
};

export default columnTypes;
