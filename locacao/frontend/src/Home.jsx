import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import './styles.css'; // Importa o arquivo de estilos

function Home() {
  const [acomodacoes, setAcomodacoes] = useState([]);
  const [cidade, setCidade] = useState("");
  const [favorites, setFavorites] = useState([]);
  const baseURL = "http://127.0.0.1:8000"; // URL do backend

  // Carrega os favoritos do localStorage
  useEffect(() => {
    const storedFavorites = localStorage.getItem("favorites");
    if (storedFavorites) {
      setFavorites(JSON.parse(storedFavorites));
    }
  }, []);

  // Função para buscar acomodações
  const buscarAcomodacoes = async (cidadeBusca = "") => {
    let url = `${baseURL}/acomodacoes`;
    if (cidadeBusca) {
      url += `?cidade=${cidadeBusca}`;
    }
    try {
      const response = await fetch(url);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const data = await response.json();
      setAcomodacoes(data);
    } catch (error) {
      console.error("Erro ao buscar acomodações:", error);
    }
  };

  useEffect(() => {
    buscarAcomodacoes();
  }, []);

  // Função para alternar o status de favorito
  const toggleFavorite = (acomodacao) => {
    let updatedFavorites;
    if (favorites.find((item) => item.id === acomodacao.id)) {
      updatedFavorites = favorites.filter((item) => item.id !== acomodacao.id);
    } else {
      updatedFavorites = [...favorites, acomodacao];
    }
    setFavorites(updatedFavorites);
    localStorage.setItem("favorites", JSON.stringify(updatedFavorites));
  };

  // Verifica se a acomodação está favoritada
  const isFavorited = (acomodacao) =>
    favorites.some((item) => item.id === acomodacao.id);

  // Função auxiliar para construir a URL completa da imagem
  const getImageSrc = (imagem) => {
    if (imagem.startsWith("http")) {
      return imagem;
    }
    return `${baseURL}${imagem}`;
  };

  return (
    <div className="container">
      <header className="header">
        <h1>Lista de Acomodações</h1>
        <div className="input-group">
          <input
            type="text"
            placeholder="Digite a cidade..."
            value={cidade}
            onChange={(e) => setCidade(e.target.value)}
          />
          <button className="button" onClick={() => buscarAcomodacoes(cidade)}>
            Buscar
          </button>
        </div>
      </header>
      <div className="cards-container">
        {acomodacoes.length > 0 ? (
          acomodacoes.map((acomodacao) => (
            <div key={acomodacao.id} className="card">
              <h2>{acomodacao.nome}</h2>
              <p>
                {acomodacao.cidade} - R$ {acomodacao.preco.toFixed(2)}
              </p>
              {acomodacao.imagem && (
                <img
                  src={getImageSrc(acomodacao.imagem)}
                  alt={acomodacao.nome}
                />
              )}
              <button
                className="button"
                onClick={() => toggleFavorite(acomodacao)}
              >
                {isFavorited(acomodacao)
                  ? "Remover dos favoritos"
                  : "Favoritar"}
              </button>
              <Link className="button" to={`/accommodation/${acomodacao.id}`}>
                Ver detalhes
              </Link>
            </div>
          ))
        ) : (
          <p>Nenhuma acomodação encontrada.</p>
        )}
      </div>
      <section className="favorites-list">
        <h2>Favoritos</h2>
        {favorites.length > 0 ? (
          <ul>
            {favorites.map((fav) => (
              <li key={fav.id}>
                <strong>{fav.nome}</strong> - {fav.cidade} - R${" "}
                {fav.preco.toFixed(2)}
              </li>
            ))}
          </ul>
        ) : (
          <p>Nenhuma acomodação favoritada.</p>
        )}
      </section>
    </div>
  );
}

export default Home;
