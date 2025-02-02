// import '/public/css/pages/sg/newRequisition.css';
import React, { useEffect } from 'react';
import { useForm } from '@inertiajs/react';

const NewRequisition = () => {
	useEffect(() => {
		document.title = "Novo requerimento";
	}, []);

	const { data, setData, post, processing, errors } = useForm({
		name: '',
		email: '',
		nusp: '',
		course: '',
		requestedDiscName: '',
		requestedDiscType: '',
		requestedDiscCode: '',
		requestedDiscDepartment: '',
		takenDiscNames: '',
		takenDiscInstitutions: '',
		takenDiscCodes: '',
		takenDiscYears: '',
		takenDiscGrades: '',
		takenDiscSemesters: ''
	});

	function submit(e) {
		console.log("submitted");
		e.preventDefault();
		post('/teste-form');
	}

	return (
		<div className="content">
			<header>
				<h1>Novo requerimento</h1>
				<a href=/*{route('sg.list')}*/"#" className="button">Voltar</a>
			</header>
			<p className="instruction">
				Preencha o seguinte formulário para criar o requerimento: <br />
				(Crie um formulário para cada matéria a ser dispensada)
			</p>

			<form onSubmit={submit} id="requisition-form">
				<fieldset className="personal">
					<legend>Dados pessoais</legend>
					<div className="name-wrapper">
						<label htmlFor="name" className="name"> Nome completo </label>
						<input type="text" name="name" className="large-field" value={data.name} onChange={e => setData('name', e.target.value)} required />
						<label htmlFor="email" className="email"> Email </label>
						<input type="email" name="email" className="large-field" value={data.email} required onChange={e => setData('email', e.target.value)} />
					</div>
					<div className="nusp-email-wrapper">
						<label htmlFor="nusp" className="nusp"> Número USP </label>
						<input type="text" name="nusp" value={data.nusp} required onChange={e => setData('nusp', e.target.value)} />
					</div>
				</fieldset>

				<fieldset className="course">
					<legend> Curso </legend>
					<div className="course-wrapper">
						<label htmlFor="course" className="select"> Nome </label>
						<select name="course" value={data.course} onChange={e => setData('course', e.target.value)} required>
							<option value="">Selecione o seu curso atual</option>
							<option value="Bacharelado em Ciência da Computação">Bacharelado em Ciência da Computação</option>
							<option value="Bacharelado em Estatística">Bacharelado em Estatística</option>
							<option value="Bacharelado em Matemática">Bacharelado em Matemática</option>
							<option value="Bacharelado em Matemática Aplicada">Bacharelado em Matemática Aplicada</option>
							<option value="Bacharelado em Matemática Aplicada e Computacional">Bacharelado em Matemática Aplicada e Computacional</option>
							<option value="Licenciatura em Matemática">Licenciatura em Matemática</option>
						</select>
					</div>
				</fieldset>

				<fieldset className="disciplines">
					<legend>Disciplinas</legend>
					<div className="requested">
						<div className="disc-title">Disciplina a ser dispensada</div>
						<p className="instruction">Adicione aqui as informações da disciplina a ser dispensada</p>
						<div className="disc">
							<label htmlFor='requested-disc-name' className="requested-disc-name"> Nome </label>
							<input type="text" id="requested-disc-name" name="requested-disc-name" value={data.requestedDiscName} onChange={e => setData('requestedDiscName', e.target.value)} required />
							<div className="disc-middle-row">
								<label htmlFor="requested-disc-type" className="requested-disc-type"> Tipo </label>
								<select name="requested-disc-type" id="requested-disc-type" value={data.requestedDiscType} onChange={e => setData('requestedDiscType', e.target.value)} required>
									<option value="">Selecione o tipo</option>
									<option value="Obrigatória">Obrigatória</option>
									<option value="Optativa Eletiva">Optativa Eletiva</option>
									<option value="Optativa Livre">Optativa Livre</option>
									<option value="Extracurricular">Extracurricular</option>
								</select>

								<label htmlFor="requested-disc-code" className="requested-disc-code">
									Sigla
								</label>
								<input type="text" name="requested-disc-code" id="requested-disc-code" value={data.requestedDiscCode} onChange={e => setData('requestedDiscCode', e.target.value)} required />
							</div>

							<label htmlFor="requested-disc-department" className="disc-department">
								Departamento
							</label>
							<select name="requested-disc-department" value={data.requestedDiscDepartment} onChange={e => setData('requestedDiscDepartment', e.target.value)} required>
								<option value="">Selecione o departamento</option>
								<option value="MAC">MAC</option>
								<option value="MAE">MAE</option>
								<option value="MAP">MAP</option>
								<option value="MAT">MAT</option>
								<option value="Disciplina de fora do IME">Disciplina de fora do IME</option>
							</select>
						</div>
					</div>

					<div className="taken">
						<div className="disc-title">Disciplinas cursadas</div>
						<p className="instruction">Adicione aqui as disciplinas cursadas a serem utilizadas para a dispensa</p>
						<div className="disc-list">
							<div className="disc">
								<label htmlFor="taken-disc-name" className="disc-name"> Nome </label>
								<input type="text" name="taken-disc-name" id="taken-disc-name" value={data.takenDiscName} onChange={e => setData('takenDiscName', e.target.value)} required />
								<label htmlFor="taken-disc-institution" className="disc-institution"> Instituição em que foi cursada </label>
								<input type="text" name="taken-disc-institution" id="taken-disc-institution" value={data.takenDiscInstitution} onChange={e => setData('takenDiscInstitution', e.target.value)} required />
								<div className="disc-middle-row">
									<label htmlFor="taken-disc-code" className="disc-code"> Sigla </label>
									<input type="text" name="taken-disc-code" id="taken-disc-code" value={data.takenDiscCode} onChange={e => setData('takenDiscCode', e.target.value)} />
									<label htmlFor="taken-disc-year" className="disc-year"> Ano </label>
									<input type="text" name="taken-disc-year" id="taken-disc-year" value={data.takenDiscYear} onChange={e => setData('takenDiscYear', e.target.value)} required />
									<label htmlFor="taken-disc-grade" className="disc-grade"> Nota  </label>
									<input type="text" name="taken-disc-grade" id="taken-disc-grade" value={data.takenDiscGrade} onChange={e => setData('takenDiscGrade', e.target.value)} required />
								</div>
								<div className="disc-last-row">
									<label htmlFor="taken-disc-semester" className="disc-semester"> Semestre </label>
									<select name="taken-disc-semester" id="taken-disc-semester" value={data.takenDiscSemester} onChange={e => setData('takenDiscSemester', e.target.value)} required>
										<option value="">Selecione o semestre</option>
										<option value="Primeiro">Primeiro</option>
										<option value="Segundo">Segundo</option>
										<option value="Anual">Anual</option>
									</select>
								</div>
							</div>
						</div>
						<div className="disc-management"> 
							<button type="button" className="button add-disc" onClick={() => addDiscipline()}>Adicionar outra<br /> disciplina</button>
							<button type="button" className="button remove-disc" onClick={() => removeDiscipline()}>Remover<br /> disciplina</button>
						</div>
					</div>

					<input type="hidden" name="takenDiscCount" id="taken-disc-count" value={data.takenDiscCount} />
				</fieldset>

			</form>

			<div className="bottom-nav"> 
				<a href="{{ route('sg.list') }}" className="button">Voltar</a>
				<button type="submit" form="requisition-form" className="button">Encaminhar para análise</button>
			</div>
		</div>
	);
};

export default NewRequisition;
