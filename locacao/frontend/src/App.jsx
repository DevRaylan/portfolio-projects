import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import Home from "./Home";
import AccommodationDetails from "./AccommodationDetails";

function App() {
  return (
    <Router>
      <Routes>
        <Route path="/" element={<Home />} />
        <Route path="/accommodation/:id" element={<AccommodationDetails />} />
      </Routes>
    </Router>
  );
}

export default App;
