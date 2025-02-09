let columnTypes = {};

columnTypes.id = {
    header: 'ID',
    accessorKey: 'id',
    enableHiding: false,
    size: 0
};

columnTypes.created_at = {
    header: 'Data de criação',
    accessorFn: (row) => row.created_at.slice(0, 10),
    enableHiding: false,
    size: 0
};

columnTypes.updated_at = {
    header: 'Última modificação',
    accessorFn: (row) => row.updated_at.slice(0, 10),
    enableHiding: false,
    enableColumnActions: false,
    size: 0
};

columnTypes.requested_disc = {
    header: 'Disciplina requerida',
    accessorKey: 'requested_disc',
    enableHiding: false,
    size: 260
};

columnTypes.situation = {
    header: 'Situação',
    accessorKey: 'situation',
    enableHiding: false,
    size: 700,
    grow: true
};

columnTypes.internal_status = {
    header: 'Situação',
    accessorKey: 'internal_status',
    enableHiding: false,
    size: 400
};

columnTypes.student_name = {
    header: 'Aluno',
    accessorKey: 'student_name',
    enableHiding: false,
    size: 120
};

columnTypes.student_nusp = {
    header: 'Número USP',
    accessorKey: 'student_nusp',
    enableHiding: false,
    size: 0
};

columnTypes.department = {
    header: 'Departamento',
    accessorKey: 'department',
    enableHiding: false,
    size: 160
};

export default columnTypes;