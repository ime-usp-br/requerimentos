import React, { useState, useEffect, useRef } from 'react';
import { Stack, Typography, Button, Alert, Box, Chip, Tooltip } from "@mui/material";
import { styled } from '@mui/material/styles';
import { CloudUpload, Description, Undo } from '@mui/icons-material';

const VisuallyHiddenInput = styled('input')({
    clip: 'rect(0 0 0 0)',
    clipPath: 'inset(50%)',
    height: 1,
    overflow: 'hidden',
    position: 'absolute',
    bottom: 0,
    left: 0,
    whiteSpace: 'nowrap',
    width: 1,
});

const DocumentField = ({ title, documentKey, data, setData, isUpdate, errors}) => {
    const [newFile, setNewFile] = useState(null);
    const [fileSizeError, setFileSizeError] = useState(null);
    const MAX_FILE_SIZE = 512 * 1024;
    
    const originalDocRef = useRef(null);
    
    if (originalDocRef.current === null) {
        const initial = data[documentKey];
        if (initial) {
            originalDocRef.current = initial;
        }
    }
    
    const originalDoc = originalDocRef.current;
        
    const handleFileChange = (e) => {
        const file = e.target.files[0];
        if (file) {

            if (file.size > MAX_FILE_SIZE) {
                setFileSizeError(`Faça o upload de arquivos de no máximo 512KB. Tamanho do arquivo enviado: ${(file.size / 1024).toFixed(2)}KB`);
                e.target.value = '';
                return;
            }
            
            setFileSizeError(null);
            setNewFile(file);
            setData(documentKey, file);
        } 
    };

    const handleReturnToOriginal = () => {
        setNewFile(null);
        const fileInput = document.querySelector(`input[type="file"][data-key="${documentKey}"]`);
        if (fileInput) {
            fileInput.value = '';
        }
        
        if (originalDoc) {
            setData(documentKey, originalDoc);
        } else {
            setData(documentKey, null);
        }        
    };

    const handleViewDocument = () => {
        if (originalDoc && originalDoc.url) {
            window.open(originalDoc.url, '_blank');
        }
    };

    const formatDate = (dateString) => {
        return new Date(dateString).toLocaleDateString('pt-BR');
    };

    return (
        <Box>
            <Typography variant={"subtitle1"}>{title}</Typography>
            {errors[documentKey] && (
                <Alert severity="error" sx={{ mb: 1 }}>
                    {errors[documentKey]}
                </Alert>
            )}
            {fileSizeError && (
                <Alert severity="error" sx={{ mb: 1 }}>
                    {fileSizeError}
                </Alert>
            )}
            <Stack direction={{ sm: "row"}} spacing={{ sm: 2}} sx={{alignItems: "center", flexWrap: "wrap", gap: 1}}>
                <Button
                    component="label"
                    variant="contained"
                    startIcon={<CloudUpload />}
                    color={"primary"}
                >
                    {originalDoc || newFile ? "Substituir arquivo" : "Anexar arquivo"}
                    <VisuallyHiddenInput
                        type="file"
                        onChange={handleFileChange}
                        accept=".pdf"
                        required={!originalDoc}
                        data-key={documentKey}
                    />
                </Button>
                
                {newFile && (
                    <Stack direction="row" spacing={1} alignItems="center">
                        <Chip
                            icon={<Description />}
                            label={`Novo: ${newFile.name}`}
                            color="primary"
                            variant="outlined"
                            onDelete={handleReturnToOriginal}
                            deleteIcon={isUpdate && <Undo />}
                        />
                    </Stack>
                )}
                
                {originalDoc && !newFile && (
                    <Tooltip title="Clique para visualizar o documento" arrow>
                        <Chip
                            icon={<Description />}
                            label={`Documento atual, enviado em ${formatDate(originalDoc.created_at)}`}
                            color="success"
                            variant="outlined"
                            onClick={handleViewDocument}
                            sx={{ 
                                cursor: 'pointer'
                            }}
                        />
                    </Tooltip>
                )}
            </Stack>
        </Box>
    );
};

const DocumentsUpload = ({data, setData, isUpdate = false, errors = {}}) => {
    return (
        <Stack spacing={1.5}>
            <Typography variant={"h6"} component={"legend"}> Documentos </Typography>
            <Alert severity="info"> 
                Adicione apenas documentos relacionados às matérias desse pedido.
            </Alert>

            <DocumentField
                title="Histórico escolar da instituição de origem (PDF, max 512KB)"
                documentKey="takenDiscRecord"
                data={data}
                setData={setData}
                isUpdate={isUpdate}
                errors={errors}
            />

            <DocumentField
                title="Histórico escolar do curso atual (máx. 512KB)"
                documentKey="courseRecord"
                data={data}
                setData={setData}
                isUpdate={isUpdate}
                errors={errors}
            />

            <DocumentField
                title="Ementas das disciplinas cursadas (máx. 512KB)"
                documentKey="takenDiscSyllabus"
                data={data}
                setData={setData}
                isUpdate={isUpdate}
                errors={errors}
            />

            <DocumentField
                title="Ementa da disciplina a ser dispensada (máx. 512KB)"
                documentKey="requestedDiscSyllabus"
                data={data}
                setData={setData}
                isUpdate={isUpdate}
                errors={errors}
            />
        </Stack>
    );
};

export default DocumentsUpload;