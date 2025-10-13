import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { MapPin } from "lucide-react";

const MapView = () => {
  return (
    <Card className="shadow-soft">
      <CardHeader>
        <CardTitle className="flex items-center gap-2 text-2xl">
          <MapPin className="h-6 w-6 text-primary" />
          Mapa de Obras
        </CardTitle>
      </CardHeader>
      <CardContent>
        <div className="relative h-[400px] overflow-hidden rounded-lg bg-muted">
          {/* Placeholder for map - will be integrated with real mapping solution */}
          <div className="flex h-full items-center justify-center">
            <div className="text-center">
              <MapPin className="mx-auto h-16 w-16 text-muted-foreground" />
              <p className="mt-4 text-muted-foreground">
                Mapa interativo será integrado
              </p>
              <p className="mt-2 text-sm text-muted-foreground">
                Visualização geoespacial de obras em desenvolvimento
              </p>
            </div>
          </div>
        </div>
      </CardContent>
    </Card>
  );
};

export default MapView;