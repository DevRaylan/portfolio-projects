interface Construction {
  id: string;
  name: string;
  city: string;
  state: string;
  status: string;
  estimated_value: number | null;
  contact_name: string | null;
  contact_phone: string | null;
  contact_email: string | null;
  buyer_name: string | null;
  buyer_phone: string | null;
  buyer_email: string | null;
  location: string;
  created_at: string;
}

export const exportConstructionsToCSV = (constructions: Construction[]) => {
  // Define CSV headers
  const headers = [
    "Nome da Obra",
    "Cidade",
    "Estado",
    "Endereço",
    "Status",
    "Valor Estimado",
    "Responsável",
    "Telefone Responsável",
    "Email Responsável",
    "Comprador",
    "Telefone Comprador",
    "Email Comprador",
    "Data de Cadastro",
  ];

  // Convert data to CSV rows
  const rows = constructions.map((c) => [
    c.name,
    c.city,
    c.state,
    c.location,
    c.status === "high" ? "Alta" : c.status === "medium" ? "Média" : "Baixa",
    c.estimated_value ? `R$ ${c.estimated_value.toLocaleString("pt-BR")}` : "-",
    c.contact_name || "-",
    c.contact_phone || "-",
    c.contact_email || "-",
    c.buyer_name || "-",
    c.buyer_phone || "-",
    c.buyer_email || "-",
    new Date(c.created_at).toLocaleDateString("pt-BR"),
  ]);

  // Create CSV content
  const csvContent = [
    headers.join(","),
    ...rows.map((row) =>
      row.map((cell) => `"${cell.toString().replace(/"/g, '""')}"`).join(",")
    ),
  ].join("\n");

  // Create blob and download
  const blob = new Blob(["\uFEFF" + csvContent], {
    type: "text/csv;charset=utf-8;",
  });
  const link = document.createElement("a");
  const url = URL.createObjectURL(blob);
  
  link.setAttribute("href", url);
  link.setAttribute(
    "download",
    `construlink_obras_${new Date().toISOString().split("T")[0]}.csv`
  );
  link.style.visibility = "hidden";
  
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
};
