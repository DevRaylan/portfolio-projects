package com.raylan.calculadoragorjetas.repository;

import com.raylan.calculadoragorjetas.model.Gorjeta;
import org.springframework.data.jpa.repository.JpaRepository;

import java.util.List;

public interface GorjetaRepository extends JpaRepository<Gorjeta, Long> {
    List<Gorjeta> findByAtendenteId(Long atendenteId);
}