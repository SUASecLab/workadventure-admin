#!/usr/bin/python3
import json

textures = open("woka.json", "r")
data = json.load(textures)

def generateLayerTextures(layer):
    output = open("layer_" + layer + ".js", "w");
    for layerCollection in data[layer]["collections"]:
        output.write("db.textures.insert([\n")
        for layerTexture in layerCollection["textures"]:
            output.write("    {\n")
            output.write("        \"waId\": \"" + layerTexture["id"] + "\",\n")
            output.write("        \"url\": \"" + layerTexture["url"] + "\",\n")
            output.write("        \"layer\": \"" + layer + "\"\n")
            output.write("    },\n")
        output.write("]);\n\n")
        output.close();

generateLayerTextures("woka")
generateLayerTextures("body")
generateLayerTextures("eyes")
generateLayerTextures("hair")
generateLayerTextures("clothes")
generateLayerTextures("hat")
generateLayerTextures("accessory")

textures.close();

print("Generated woka information")
