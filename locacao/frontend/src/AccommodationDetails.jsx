import { useEffect, useState } from "react";
import { useParams, Link } from "react-router-dom";
import './styles.css';

function AccommodationDetails() {
  const { id } = useParams();
  const [acomodacao, setAcomodacao] = useState(null);
  const baseURL = "http://127.0.0.1:8000";

  useEffect(() => {
    const fetchDetails = async () => {
      try {
        const response = await fetch(`${baseURL}/acomodacoes/${id}`);
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        setAcomodacao(data);
      } catch (error) {
        console.error("Erro ao buscar detalhes:", error);
      }
    };
    fetchDetails();
  }, [id]);

  const getImageSrc = (imagem) => {
    if (imagem.startsWith("http")) {
      return imagem;
    }
    return `${baseURL}${imagem}`;
  };

  if (!acomodacao) {
    return <div className="container">Carregando detalhes...</div>;
  }

  return (
    <div className="container">
      <header className="header">
        <h1>{acomodacao.nome}</h1>
      </header>
      <div className="card">
        <p>
          <strong>Cidade:</strong> {acomodacao.cidade}
        </p>
        <p>
          <strong>Preço:</strong> R$ {acomodacao.preco.toFixed(2)}
        </p>
        <p>
          <strong>Descrição:</strong> {acomodacao.descricao}
        </p>
        {acomodacao.imagem && (
          <img
            src={getImageSrc(acomodacao.imagem)}
            alt={acomodacao.nome}
          />
        )}
      </div>
      <Link className="button" to="/">
        Voltar para a lista
      </Link>
    </div>
  );
}

export default AccommodationDetails;
