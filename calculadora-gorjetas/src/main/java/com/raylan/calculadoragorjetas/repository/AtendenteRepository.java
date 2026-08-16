package com.raylan.calculadoragorjetas.repository;

import com.raylan.calculadoragorjetas.model.Atendente;
import org.springframework.data.jpa.repository.JpaRepository;

public interface AtendenteRepository extends JpaRepository<Atendente, Long> {
}