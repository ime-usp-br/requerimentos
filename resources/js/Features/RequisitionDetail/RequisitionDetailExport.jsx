import React from 'react';

const RequisitionDetailExport = ({ requisition }) => {
    const containerStyle = {
        width: '1000px',
        margin: '20px auto',
        padding: '20px',
        background: '#fff',
        boxShadow: '0 0 5px rgba(0,0,0,0.2)',
        fontFamily: 'Arial, sans-serif'
    };

    const sectionStyle = {
        marginBottom: '20px',
        paddingBottom: '10px',
        borderBottom: '1px solid #ddd'
    };

    const sectionTitleStyle = {
        fontWeight: '600',
        marginBottom: '8px',
        fontSize: '1.1em'
    };

    const textStyle = {
        margin: '4px 0'
    };

    const tableStyle = {
        width: '100%',
        borderCollapse: 'collapse',
        marginTop: '10px'
    };

    const cellStyle = {
        border: '1px solid #ccc',
        padding: '6px 8px',
        textAlign: 'left'
    };

    return (
        <div style={containerStyle}>
            {/* Dados Pessoais */}
            <div style={sectionStyle}>
                <h6 style={sectionTitleStyle}>Dados Pessoais</h6>
                <p style={textStyle}><strong>Nome:</strong> {requisition.student_name}</p>
                <p style={textStyle}><strong>Email:</strong> {requisition.email}</p>
                <p style={textStyle}><strong>Número USP:</strong> {requisition.student_nusp}</p>
            </div>

            {/* Curso Atual */}
            <div style={sectionStyle}>
                <h6 style={sectionTitleStyle}>Curso Atual</h6>
                <p style={textStyle}>{requisition.course}</p>
            </div>

            {/* Disciplina Requerida */}
            <div style={sectionStyle}>
                <h6 style={sectionTitleStyle}>Disciplina Requerida</h6>
                <p style={textStyle}><strong>Código:</strong> {requisition.requested_disc_code}</p>
                <p style={textStyle}><strong>Nome:</strong> {requisition.requested_disc}</p>
                <p style={textStyle}><strong>Tipo:</strong> {requisition.requested_disc_type}</p>
                <p style={textStyle}><strong>Departamento:</strong> {requisition.department}</p>
            </div>

            {/* Disciplinas Cursadas */}
            <div style={sectionStyle}>
                <h6 style={sectionTitleStyle}>Disciplinas Cursadas</h6>
                <table style={tableStyle}>
                    <thead>
                        <tr>
                            <th style={cellStyle}>Nome</th>
                            <th style={cellStyle}>Instituição</th>
                            <th style={cellStyle}>Código</th>
                            <th style={cellStyle}>Ano</th>
                            <th style={cellStyle}>Nota</th>
                            <th style={cellStyle}>Semestre</th>
                        </tr>
                    </thead>
                    <tbody>
                        {requisition.takenDiscs.map((disc, index) => (
                            <tr key={index}>
                                <td style={cellStyle}>{disc.name}</td>
                                <td style={cellStyle}>{disc.institution}</td>
                                <td style={cellStyle}>{disc.code}</td>
                                <td style={cellStyle}>{disc.year}</td>
                                <td style={cellStyle}>{disc.grade}</td>
                                <td style={cellStyle}>{disc.semester}</td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>

            {/* Observações */}
            <div style={sectionStyle}>
                <h6 style={sectionTitleStyle}>Observações</h6>
                <p style={textStyle}>{requisition.observations}</p>
            </div>
        </div>
    );
};

export default RequisitionDetailExport;