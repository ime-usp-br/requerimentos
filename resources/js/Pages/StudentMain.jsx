import React from 'react';
import { DataGrid } from '@mui/x-data-grid';

// import '../../../public/css/global.css';
// import '../../../public/css/pages/home.css';
// import '../../../public/css/components/footer.css';
// import '../../../public/css/components/overlay.css';
import '../../../public/css/pages/student/list.css';

const columns = [
    { field: 'id', headerName: 'ID', width: 90 },
    {
      field: 'created_at',
      headerName: 'Data de criação',
      width: 200,
    },
    // {
    //   field: 'takenDiscipline',
    //   headerName: 'Disciplina cursada',
    //   width: 150,
    // },
    {
      field: 'requested_disc',
      headerName: 'Disciplina requerida',
      width: 240,
    },
    {
      field: 'situation',
      headerName: 'Situação',
      width: 500,
    },
];

export default function StudentMain({ requisitions, requisition_period_status }) {
    let formattedRequisitions = requisitions.map((e) => {
        return {
            id: e.id,
            created_at : e.created_at.slice(0, 10),
            requested_disc: e.requested_disc,
            situation: e.situation
        }
    })
    return (
        <div className='student-body'>
            <header className='student-header'>
                <h1 className='student-h1'>Requerimentos</h1>
                <nav className='student-nav'>
                    {requisition_period_status ? 
                        <a href="{{ route('student.newRequisition') }}" class="student-button">Criar requerimento</a>
                        : null
                    }
                    
                    <form action="/" method="GET" id="form" />
                    <button type="submit" form="form" className="student-button">Sair</button>
                </nav>
            </header>
            <div className='student-content'>
                <DataGrid
                    rows={formattedRequisitions}
                    columns={columns}
                    initialState={{
                        pagination: {
                            paginationModel: {
                                pageSize: 10,
                            },
                        },
                    }}
                    disableRowSelectionOnClick
                />
            </div>
        </div>
    );
};