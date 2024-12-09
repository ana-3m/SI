<?php
session_start();

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar os dados enviados pelo formulário
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $carro_matricula = $_POST['carro'] ?? '';
    $data_ini = $_POST['data_ini'] ?? '';
    $data_fim = $_POST['data_fim'] ?? '';

    // Conectar ao banco de dados
    $conn = pg_connect("host=localhost dbname=postgres user=postgres password=postgres");

    if (!$conn) {
        die("Erro ao conectar ao banco de dados: " . pg_last_error());
    }

    // Gerar um ID de reserva único
    $id = rand(100000, 999999); // Gera um número aleatório de 6 dígitos

    // Inserir a nova reserva na tabela de reservas
    $query = "
        INSERT INTO reserva (id, carro_matricula, data_ini, data_fim, cliente_pessoa_email) 
        VALUES ($1, $2, $3, $4, $5)
    ";

    $result = pg_query_params($conn, $query, [
        $id, $carro_matricula, $data_ini, $data_fim, $email
    ]);

    // Verificar se a inserção foi bem-sucedida
    if ($result) {
        // Atualizar a visibilidade do carro alugado para FALSE
        $update_query = "UPDATE carro SET visivel = FALSE WHERE matricula = $1";
        pg_query_params($conn, $update_query, [$carro_matricula]);

        // Redirecionar ou exibir mensagem de sucesso
        echo "<h1>Reserva criada com sucesso!</h1>";
        echo "<p>ID da reserva: $id</p>";
        echo "<a href='../reservas.php'>Voltar</a>";
    } else {
        echo "Erro ao criar reserva: " . pg_last_error($conn);
    }

    // Fechar a conexão com o banco de dados
    pg_close($conn);
} else {
    // Redirecionar caso o arquivo seja acessado diretamente sem envio do formulário
    header("Location: ../reservas.php");
    exit();
}

