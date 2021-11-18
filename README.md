#Implementação de uma API REST

Trabalho da disciplina de Sistemas Distribuidos

###Time de desenvolvimento:
- Israel dos Santos Elias
- Janaina Ferreira da Silva
- Juliana Nascimento Silva

###Tecnologias usadas:
- PHP
- HTTP 1.1
- JSON

###Collection Postman 
https://www.getpo2stman.com/collections/a757678dd1419f77dbe1

### Endpoints
    
    Lista todos os alunos cadastrados<br>
    {URL}/students/list<br>
    Método: GET


#### Buscar Aluno
    Busca um aluno filtrando por RA ou por CPF<br>
    {URL}/students/student?cpf=valor OU {URL}/students/student?ra=valor<br>
    Método: GET


#### Criar Aluno
    Cadastra um novo aluno<br>
    {URL}/students/createStudent<br>
    Método: POST
    Body: 
        {
            "nome": string,
            "curso": string,
            "semestre": int,
            "ra": int, //obrigatório
            "cpf": int, //obrigatório
            "cidade": string
        }


#### Atualizar Aluno
    Atualiza um aluno<br>
    {URL}/students/updateStudent<br>
    Método: PUT
    Body: 
        {
            "nome": string,
            "curso": string,
            "semestre": int,
            "ra": int, //obrigatório
            "cpf": int, //obrigatório
            "cidade": string
        }


#### Deletar Aluno
    Deleta um aluno<br>
    {URL}/students/deleteStudent?cpf=valor OU {URL}/students/deleteStudent?ra=valor<br>
    Método: DELETE