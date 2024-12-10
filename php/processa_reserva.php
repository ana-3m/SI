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

    // Verificar se o carro está disponível no período solicitado
    $disponibilidade_query = "
        SELECT 1 FROM reserva
        WHERE carro_matricula = $1
          AND (
            (data_ini <= $2 AND data_fim >= $2) OR
            (data_ini <= $3 AND data_fim >= $3) OR
            (data_ini >= $2 AND data_fim <= $3)
          )
    ";
    $disponibilidade_result = pg_query_params($conn, $disponibilidade_query, [$carro_matricula, $data_ini, $data_fim]);

    if (pg_num_rows($disponibilidade_result) > 0) {
        // Carro não está disponível
        echo "<h1>Carro não disponível!</h1>";
        echo "<p>O carro com matrícula $carro_matricula já está reservado neste período.</p>";
        echo "<a href='../reservas.php'>Voltar</a>";
        pg_close($conn);
        exit();
    }

    // Calcular a quantidade de dias da reserva
    $date_ini_obj = new DateTime($data_ini);
    $date_fim_obj = new DateTime($data_fim);
    $interval = $date_ini_obj->diff($date_fim_obj);
    $days = $interval->days;

    // Verificar saldo do cliente
    $saldo_query = "SELECT saldo FROM cliente WHERE pessoa_email = $1";
    $saldo_result = pg_query_params($conn, $saldo_query, [$email]);

    // Consulta para buscar o preço do carro na tabela carro
    $preco_query = "SELECT preco FROM carro WHERE matricula = $1";
    $preco_result = pg_query_params($conn, $preco_query, array($carro_matricula));

    if ($preco_result) {
        $preco_row = pg_fetch_assoc($preco_result);
        if ($preco_row) {
            $preco = $preco_row['preco']; // Obter o preço do carro

            if ($saldo_result) {
                $saldo_row = pg_fetch_assoc($saldo_result);
                $saldo = $saldo_row['saldo'];

                // Calcular o saldo necessário
                $saldo_necessario = $days * $preco;

                if ($saldo < $saldo_necessario) {
                    // Saldo insuficiente
                    echo "<h1>Saldo insuficiente!</h1>";
                    echo "<p>Você precisa de $saldo_necessario unidades de saldo, mas tem apenas $saldo.</p>";
                    echo "<a href='../reservas.php'>Voltar</a>";
                    pg_close($conn);
                    exit();
                }
            } else {
                echo "Erro ao verificar saldo do cliente: " . pg_last_error($conn);
                pg_close($conn);
                exit();
            }
        } else {
            echo "Carro não encontrado.";
            pg_close($conn);
            exit();
        }
    } else {
        echo "Erro ao buscar o preço do carro: " . pg_last_error($conn);
        pg_close($conn);
        exit();
    }

    // Gerar um ID de reserva único
    $id = rand(100000, 999999); // Gera um número aleatório de 6 dígitos

    // Inserir a nova reserva na tabela de reservas
    $preco_pago = $saldo_necessario; // O preço pago é o saldo necessário para a reserva
    $query = "
        INSERT INTO reserva (id, carro_matricula, data_ini, data_fim, cliente_pessoa_email, preco_pago) 
        VALUES ($1, $2, $3, $4, $5, $6)
    ";

    $result = pg_query_params($conn, $query, [
        $id, $carro_matricula, $data_ini, $data_fim, $email, $preco_pago
    ]);

    // Verificar se a inserção foi bem-sucedida
    if ($result) {
        // Subtrair o saldo necessário
        $update_saldo_query = "
            UPDATE cliente 
            SET saldo = saldo - $1 
            WHERE pessoa_email = $2
        ";
        pg_query_params($conn, $update_saldo_query, [$saldo_necessario, $email]);

        // Redirecionar ou exibir mensagem de sucesso
        echo "<h1>Reserva criada com sucesso!</h1>";
        echo "<p>ID da reserva: $id</p>";
        echo "<p>Foi debitado $saldo_necessario unidades do saldo do cliente.</p>";
        echo "<p>Preço pago: $preco_pago unidades.</p>";
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
