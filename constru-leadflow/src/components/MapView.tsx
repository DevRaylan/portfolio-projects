import { useEffect, useRef } from "react";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { MapPin } from "lucide-react";
import L from "leaflet";
import "leaflet/dist/leaflet.css";

// Mock data for construction sites
const constructions = [
  { id: 1, name: "Condomínio Residencial Aurora", lat: -23.5505, lng: -46.6333, status: "high" },
  { id: 2, name: "Edifício Comercial Platina", lat: -25.4284, lng: -49.2733, status: "high" },
  { id: 3, name: "Shopping Center Norte", lat: -30.0346, lng: -51.2177, status: "medium" },
  { id: 4, name: "Residencial Parque das Flores", lat: -27.5954, lng: -48.5480, status: "medium" },
  { id: 5, name: "Galpão Industrial TechPark", lat: -26.9191, lng: -49.0661, status: "low" },
];

const MapView = () => {
  const mapRef = useRef<L.Map | null>(null);
  const mapContainerRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    if (!mapContainerRef.current) return;

    // Initialize map
    const map = L.map(mapContainerRef.current).setView([-25.4284, -49.2733], 5);
    mapRef.current = map;

    // Add OpenStreetMap tiles
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    }).addTo(map);

    // Add markers
    constructions.forEach((construction) => {
      const markerColor = 
        construction.status === "high" ? "#0F4C81" :
        construction.status === "medium" ? "#3399CC" :
        "#94A3B8";

      const customIcon = L.divIcon({
        className: "custom-marker",
        html: `
          <div style="
            background-color: ${markerColor};
            width: 32px;
            height: 32px;
            border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg);
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
          ">
            <div style="
              width: 100%;
              height: 100%;
              display: flex;
              align-items: center;
              justify-content: center;
              transform: rotate(45deg);
            ">
              <div style="
                width: 8px;
                height: 8px;
                background: white;
                border-radius: 50%;
              "></div>
            </div>
          </div>
        `,
        iconSize: [32, 32],
        iconAnchor: [16, 32],
      });

      const marker = L.marker([construction.lat, construction.lng], {
        icon: customIcon,
      }).addTo(map);

      marker.bindPopup(`
        <div style="font-family: Inter, sans-serif;">
          <strong style="font-size: 14px;">${construction.name}</strong><br/>
          <span style="font-size: 12px; color: #64748b;">
            Status: ${construction.status === "high" ? "Alta Prioridade" : 
                     construction.status === "medium" ? "Média Prioridade" : 
                     "Baixa Prioridade"}
          </span>
        </div>
      `);
    });

    // Cleanup
    return () => {
      map.remove();
    };
  }, []);

  return (
    <Card className="shadow-soft">
      <CardHeader>
        <CardTitle className="flex items-center gap-2 text-2xl">
          <MapPin className="h-6 w-6 text-primary" />
          Mapa de Obras
        </CardTitle>
      </CardHeader>
      <CardContent>
        <div 
          ref={mapContainerRef} 
          className="h-[400px] overflow-hidden rounded-lg border border-border"
        />
      </CardContent>
    </Card>
  );
};

export default MapView;