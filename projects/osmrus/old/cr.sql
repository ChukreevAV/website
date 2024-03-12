--�������� ������� ������ � ����������� (poly)
CREATE TABLE osm_polygon_without_geom AS
select *
  from osm_polygon;
ALTER TABLE osm_polygon_without_geom DROP COLUMN way;

--�������� ������� ������ � ����������� (point)
CREATE TABLE osm_point_without_geom AS
select *
  from osm_point;
ALTER TABLE osm_point_without_geom DROP COLUMN way;

--�������� ������� ������ � ����������� (roads)
CREATE TABLE osm_roads_without_geom AS
select *
  from osm_roads;
ALTER TABLE osm_roads_without_geom DROP COLUMN way;

--�������� ������� ������ � ����������� (line)
CREATE TABLE osm_line_without_geom AS
select *
  from osm_line;
ALTER TABLE osm_line_without_geom DROP COLUMN way;

ALTER TABLE osm_polygon ADD COLUMN valid text;
UPDATE osm_polygon SET valid=ST_IsValid(osm_polygon.way);