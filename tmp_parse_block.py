 1671|             "hallucinations_detected": 0,
 1672|         }
 1673| 
 1674|     wrong_jurisdiction_violations = detect_wrong_jurisdiction(analysis_text)
 1675|     wrong_jurisdiction_count = len(wrong_jurisdiction_violations)
 1676|     
 1677|     if wrong_jurisdiction_count > 0:
 1678|         print(f"⚠️ ОБНАРУЖЕНО {wrong_jurisdiction_count} УПОМИНАНИЙ РОССИЙСКИХ ЗАКОНОВ!")
 1679| 
 1680|     # Парсер рисков
 1681|     findings = []
 1682|     current = None
 1683|     
 1684|     for raw_line in (analysis_text or "").splitlines():
 1685|         line = raw_line.strip()
 1686|         
 1687|         if not line:
 1688|             if current:
 1689|                 findings.append(current)
 1690|                 current = None
 1691|             continue
 1692|         
 1693|         severity = None
 1694|         if re.search(r'\[(?:Высокий|высок)\]', line):
 1695|             severity = "high"
 1696|         elif re.search(r'\[(?:Средний|средн)\]', line):
 1697|             severity = "medium"
 1698|         elif re.search(r'\[(?:Низкий|низк)\]', line):
 1699|             severity = "low"
 1700|         
 1701|         if severity:
 1702|             if current:
 1703|                 findings.append(current)
 1704|             title = re.sub(r'\[[^\]]*\]\s*', '', line)
 1705|             title = re.sub(r'^[\s•\-\*]+', '', title)
 1706|             title = title.strip(' .:')
 1707|             if not title or len(title) < 3:
 1708|                 title = "Юридический риск"
 1709|             current = {
 1710|                 "title": title,
 1711|                 "severity": severity,
 1712|                 "point": "",
 1713|                 "fragment": "",
 1714|                 "norm": "",
 1715|                 "why": "",
 1716|                 "fix": "",
 1717|             }
 1718|             continue
 1719|         
 1720|         if current:
 1721|             m = re.search(r'Пункт\s+договора\s*:\s*(.*)', line, re.IGNORECASE)
 1722|             if m: current["point"] = m.group(1).strip()
 1723|             m = re.search(r'Фрагмент\s+договора\s*:\s*(.*)', line, re.IGNORECASE)
 1724|             if m: current["fragment"] = m.group(1).strip()
 1725|             m = re.search(r'Норма\s+РТ\s*:\s*(.*)', line, re.IGNORECASE)
 1726|             if m: current["norm"] = m.group(1).strip()
 1727|             m = re.search(r'Почему\s+риск\s*:\s*(.*)', line, re.IGNORECASE)
 1728|             if m: current["why"] = m.group(1).strip()
 1729|             m = re.search(r'Что\s+исправить\s*:\s*(.*)', line, re.IGNORECASE)
 1730|             if m: current["fix"] = m.group(1).strip()
 1731|     
 1732|     if current:
 1733|         findings.append(current)
 1734| 
 1735|     print(f"  [PARSER] Найдено рисков до фильтрации: {len(findings)}")
 1736|     
 1737|     # Фильтруем низкокачественные риски
 1738|     findings = _filter_findings_by_quality(findings, min_quality=0.45)
 1739|     findings = _filter_findings_by_contract_evidence(findings, contract_text)
 1740|     findings = _filter_findings_by_rag_evidence(findings, contract_text, rag_context)